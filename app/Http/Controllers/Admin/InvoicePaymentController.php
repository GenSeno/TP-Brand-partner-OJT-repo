<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoicePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Str;
use App\Enums\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\PaymentReceiptMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class InvoicePaymentController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Invoice $billing)
    {
        $payment_methods = PaymentMethod::getDropdown();
        return Inertia::modal('admin/invoice/payment/create', [
            'invoice' => $billing->load(['payments.creator', 'payments.media']),
            'balance' => $billing->append('summary')->summary['amount_balance'],
            'payment_methods' => $payment_methods,
        ])->baseRoute('admin.billing.show', $billing->id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoicePaymentRequest $request, Invoice $billing)
    {
        $data = $request->validated();

        // convert decimal amount (e.g. 123.45) to integer cents
        $amount = (int) round($data['amount'] * 100);

        $payment = Payment::create([
            'invoice_id' => $billing->id,
            'amount' => $amount,
            'paid_at' => $data['paid_at'],
            'internal_reference' => $data['internal_reference'] ?? null,
            'reference' => $data['reference'] ?? null,
            'method' => $data['method'] ?? null,
            'created_by' => auth()->id(),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $cleanName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();

                $payment
                    ->addMedia($file)
                    ->usingFileName("payment_{$payment->id}_{$cleanName}.{$extension}")
                    ->toMediaCollection('files', 'private');
            }
        }

        return response()->json([
            'message' => 'Payment recorded successfully.',
        ], 201);
    }

    public function upload(Request $request, Payment $payment)
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

        $uploaded = array_map(function ($file) use ($payment) {
            $originalName = $file->getClientOriginalName();
            $cleanName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();

            $finalFileName = "payment_{$payment->id}_{$cleanName}.{$extension}";

            $media = $payment
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

    public function destroyMedia(Payment $payment, $mediaId)
    {
        $media = $payment->media()->findOrFail($mediaId);
        $media->delete();

        return response()->noContent();
    }

    public function edit(Invoice $billing, Payment $payment)
    {
        $payment_methods = PaymentMethod::getDropdown();
        return Inertia::modal('admin/payment-acknowledgement/edit', [
            'invoice' => $billing->load(['payments.creator', 'payments.media']),
            'balance' => $billing->append('summary')->summary['amount_balance'],
            'payment' => $payment->load(['media']),
            'payment_methods' => $payment_methods,
        ])->baseRoute('admin.payment-acknowledgement.index');
    }

    public function update(InvoicePaymentRequest $request, Invoice $billing, Payment $payment)
    {
        $data = $request->validated();

        // convert decimal amount (e.g. 123.45) to integer cents
        $amount = (int) round($data['amount'] * 100);

      // If payment is already posted, prevent updating main fields
        if (!$payment->posted_at) {

            // convert decimal amount (e.g. 123.45) to integer cents
            $amount = (int) round($data['amount'] * 100);

            $payment->update([
                'amount'    => $amount,
                'paid_at'   => $data['paid_at'],
                'reference' => $data['reference'] ?? null,
                'method'    => $data['method'] ?? null,
            ]);
        }
        // Handle new uploaded files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $cleanName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();

                $payment
                    ->addMedia($file)
                    ->usingFileName("payment_{$payment->id}_{$cleanName}.{$extension}")
                    ->toMediaCollection('files', 'private');
            }
        }

        return response()->json([
            'message' => 'Payment updated successfully.',
            'payment' => $payment->load('media'), // return updated payment with media
        ], 200);
    }

     public function download(Invoice $billing, Payment $payment)
     {

        $billing->load(['billingAddress.country']);

        $invoice = $billing;
        return Pdf::loadView('pdf.payment-receipt', compact('invoice', 'payment'))
            ->setPaper('legal', 'portrait')
            ->download("payment-{$payment->internal_reference}.pdf");
    }

      public function sendReceipt(Invoice $billing, Payment $payment)
        {
        $billing->load(['billingAddress.country']);

        if (!$payment) {
            return redirect()
                ->route('admin.payment-acknowledgement.index')
                ->with('danger', 'No payments found for this billing.');
        }

        //$email = 'bjohnalou@gmail.com';
        $email = $billing->billingAddress?->email;

        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($email)->send(new PaymentReceiptMail($billing, $payment));

            $payment->update([
              'sent_at' => now(),
            ]);

            return redirect()
                ->route('admin.payment-acknowledgement.index')
                ->with('success', 'Payment receipt sent successfully.');
        }

        return redirect()
            ->route('admin.payment-acknowledgement.index')
            ->with('danger', 'No valid email found in billing address.');
    }

    public function viewMedia(Invoice $billing, Payment $payment, Media $media)
    {
        if ($media->model_id !== $payment->id) {
            abort(403);
        }

       return response()->file($media->getPath());
    }



}
