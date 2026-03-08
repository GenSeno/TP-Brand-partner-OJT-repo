<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Currency;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Carbon;
use App\Lunar\DataTypes\Price;

class PaymentAcknowledgementController extends Controller
{
    protected $defaultPerPage = 10;

    public function index(Request $request)
    {
        $payments = QueryBuilder::for(Payment::class)
            ->with([
                'invoice.order.orderable',
                'invoice.billingAddress',
            ])
            ->allowedSorts(['paid_at', 'amount'])
            ->defaultSort(['-paid_at'])
            ->allowedFilters([
                AllowedFilter::exact('internal_reference'),
                AllowedFilter::exact('reference'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

            // Current month period
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $currency = Currency::getDefault();

            // Total payments made this month
            $totalPaymentsThisMonth = Payment::whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            // Total amount_due of invoices due this month
            $totalAmountDueThisMonth = Invoice::whereBetween('due_at', [$startOfMonth, $endOfMonth])
                ->sum('amount_due');

            // Total amount to pay for invoices due this month
             $totalToPayThisMonth = Invoice::whereBetween('due_at', [$startOfMonth, $endOfMonth])
            ->withSum('payments', 'amount') // sum of payments for each invoice
            ->get()
            ->sum(function ($invoice) {
                // convert Price objects to numeric values
                $amountDue = $invoice->amount_due->value; // integer cents
                $paid = $invoice->payments_sum_amount ?? 0; // payments sum (already integer)
                return max($amountDue - $paid, 0);
            });

            // Number of payment acknowledgements created this month
            $paymentsAcknowledgedThisMonth = Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
            
            $stats = [
                'payments_this_month' => new Price((int) $totalPaymentsThisMonth , $currency),
                'due_this_month' => new Price((int) $totalAmountDueThisMonth , $currency),
                'topay_this_month' => new Price((int) $totalToPayThisMonth, $currency),
                'acknowledgment' => $paymentsAcknowledgedThisMonth,
            ];


            return Inertia::render('admin/payment-acknowledgement/index', [
                'payments' => $payments,
                'stats' => $stats,
                'filter' => $request->input('filter', []) + [
                    'default_per_page' => $this->defaultPerPage,
                ],
            ]);
    }
    
}
