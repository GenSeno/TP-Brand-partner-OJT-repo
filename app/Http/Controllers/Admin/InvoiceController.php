<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Lunar\DataTypes\Price;
use App\Mail\PaymentReceiptMail;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Str;

class InvoiceController extends Controller
{
    protected $defaultPerPage = 10;

    public function index(Request $request)
    {
        $invoices = QueryBuilder::for(Invoice::class)
            ->with([
                'order.orderable',
                'order.jobOrder',
                'quotation',
                'jobOrder',
            ])
            ->allowedSorts(['reference', 'invoiced_at', 'due_at', 'total', 'amount_due'])
            ->defaultSort(['-reference'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        // Stats for cards - this month and all time
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $currency = Currency::getDefault();

        $invoiceSums = Invoice::query()
            ->selectRaw("
                SUM(CASE WHEN invoiced_at BETWEEN ? AND ? THEN amount_due ELSE 0 END) as sales_this_month,
                SUM(CASE WHEN status IN (?, ?) THEN amount_due ELSE 0 END) as unpaid_partially_paid,
                SUM(CASE WHEN status = ? THEN amount_due ELSE 0 END) as overdue
            ", [
                $startOfMonth, $endOfMonth,
                InvoiceStatus::UNPAID->value, InvoiceStatus::PARTIALLY_PAID->value,
                InvoiceStatus::OVERDUE->value,
            ])
            ->first();

        $stats = [
            'sales_this_month' => new Price((int) $invoiceSums->sales_this_month, $currency),
            'paid_this_month' => new Price(
                (int) Payment::whereBetween('paid_at', [$startOfMonth, $endOfMonth])->sum('amount'),
                $currency
            ),
            'unpaid_partially_paid' => new Price((int) $invoiceSums->unpaid_partially_paid, $currency),
            'overdue' => new Price((int) $invoiceSums->overdue, $currency),
        ];

        return Inertia::render('admin/invoice/index', [
            'invoices' => $invoices,
            'stats' => $stats,
            'statusOptions' => InvoiceStatus::getOptions(),
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
        ]);
    }

    public function fetchByStatus($status)
    {
        $statuses = explode(',', $status);

        $invoices = Invoice::with(['order.orderable'])
            ->whereIn('status', $statuses)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($invoices);
    }

    public function showCustomerBillingSummary(Customer $customer)
    {
        return Inertia::modal('admin/invoice/customer/billing-summary', [
            'customer' => $customer->load([
                'invoices.order',
                'invoices.payments',
                'addresses.country',
            ]),
        ])->baseRoute('admin.billing.index');
    }

    public function show(Invoice $billing)
    {
        $billing->load([
            'order.orderable',
            'order.jobOrder',
            'lines.product',
            'billingAddress.country',
            'shippingAddress.country',
            'media',
            'payments'
        ]);
        $billing->order->append('billing_summary');
        
        $invoice = Invoice::with('payments')->find($billing->id);

        $billingUpToCurrent = $invoice->billingUpToCurrent();

        $totalBilled = $invoice->totalBilledUpToCurrent();

        $unbilledAmount = $invoice->unbilledAmountUpToCurrent();

        return Inertia::render('admin/invoice/show', [
            'invoice' => $billing,
            'billing' => $billingUpToCurrent,
            'totalBilled' => $totalBilled,
            'unbilledAmount' => $unbilledAmount,
            // 'billing' => function () use ($billing) {
            //     $billing = $billing->order->billedInvoices()->withSum('payments', 'amount')->get();
            //     return $billing->map(fn($invoice) => [
            //         ...$invoice->toArray(),
            //         'payments_sum_amount' => new Price(
            //             (int) $invoice->payments_sum_amount,
            //             $invoice->currency_code
            //         ),
            //     ]);
            // },
            'activities' => fn() => $billing->activities()->latest()->with('causer')->take(100)->get(),
        ]);
    }

    public function sendReceipt(Invoice $billing, Payment $payment)
    {
        $billing->load(['billingAddress.country']);

       // $payment = $billing->payments()->latest()->first();

        if (!$payment) {
            return redirect()
                ->route('admin.billing.show', $billing->id)
                ->with('danger', 'No payments found for this billing.');
        }

        $email = $billing->billingAddress?->email;
        //$email = 'bjohnalou@gmail.com';

        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($email)->send(new PaymentReceiptMail($billing, $payment));

             $payment->update([
              'sent_at' => now(),
            ]);


            return redirect()
                ->route('admin.billing.show', $billing->id)
                ->with('success', 'Payment receipt sent successfully.');
        }

        return redirect()
            ->route('admin.billing.show', $billing->id)
            ->with('danger', 'No valid email found in billing address.');
    }

    public function addNote(Request $request, Invoice $billing)
    {
        $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $billing->logNote($request->input('note'));

        return response()->json([
            'message' => __('Note added successfully.'),
        ], 201);
    }

    public function upload(Request $request, Invoice $billing)
    {
        $config = config('file-attachments');

        $request->validate([
            'files.*' => [
                'required',
                Rule::file()
                    ->max($config['max']['default'])
                    ->types($config['allowed_extensions']['default']),
            ]
        ]);

        $uploaded = array_map(function ($file) use ($billing) {
            $originalName = $file->getClientOriginalName();
            $cleanName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();

            $finalFileName = "{$billing->reference}_{$cleanName}.{$extension}";

            $media = $billing
                ->addMedia($file)
                ->usingFileName($finalFileName)
                ->toMediaCollection('files', 'private');

            return [
                'id' => $media->id,
                'name' => $finalFileName,
                'path' => $media->getPathRelativeToRoot(),
                'original_url' => $media->getUrl(),
                'extension' => $extension,
                'size' => $media->size,
                'created_at' => $media->created_at->toDateTimeString(),
            ];
        }, $request->file('files', []));

        return response()->json([
            'files' => $uploaded,
        ], 201);
    }

    public function destroyMedia(Invoice $billing, $mediaId)
    {
        $media = $billing->media()->findOrFail($mediaId);
        $media->delete();

        return response()->noContent();
    }

    public function share(Invoice $billing)
    {
        return response()->json([
            'url' => route('billing.shared', $billing->share_token),
        ]);
    }

    public function sharedView(string $token)
    {
        $billing = $this->findByToken($token);

        $billing->load([
            'order.orderable',
            'lines.product',
            'billingAddress.country',
            'shippingAddress.country',
        ]);

        $invoice = Invoice::with('payments')->find($billing->id);

        $billingUpToCurrent = $invoice->billingUpToCurrent();

        $totalBilled = $invoice->totalBilledUpToCurrent();

        $unbilledAmount = $invoice->unbilledAmountUpToCurrent();

        return Inertia::render('billing-share', [
            'invoice' => $billing,
            'billing' => $billingUpToCurrent,
            'totalBilled' => $totalBilled,
            'unbilledAmount' => $unbilledAmount,
        ]);
    }

    public function download(Invoice $billing)
    {
        $billing->load([
            'order.orderable',
            'lines.product',
            'billingAddress.country',
            'shippingAddress.country',
        ]);
        $invoice = Invoice::with('payments')->find($billing->id);
        $billingUpToCurrent = $invoice->billingUpToCurrent();

        $totalBilled = $invoice->totalBilledUpToCurrent();

        $unbilledAmount = $invoice->unbilledAmountUpToCurrent();

        return Pdf::loadView('pdf.billing', [
            'invoice' => $billing,
            'billing' => $billingUpToCurrent,
            'totalBilled' => $totalBilled,
            'unbilledAmount' => $unbilledAmount
            
            ])
            ->setPaper('legal', 'portrait')
            ->download("billing-{$billing->reference}.pdf");
    }

    private function findByToken(string $token): Invoice
    {
        return Invoice::where('share_token_hash', $token)->firstOrFail();
    }
}
