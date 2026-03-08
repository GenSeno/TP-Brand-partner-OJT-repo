<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderInvoiceRequest;
use App\Models\Order;
use App\Services\InvoiceService;
use Inertia\Inertia;

class OrderInvoiceController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Order $order)
    {
        $order->append('billing_summary')
            ->load(['invoices']);

        if ($order->billing_summary['amount_unbilled']->value <= 0) {
            abort(404, 'No unbilled amount to invoice.');
        }

        return Inertia::modal('admin/order/invoice/create', [
            'order' => $order,
            'types' => InvoiceType::getOptions(false, 'exclude', InvoiceType::INSTALLMENT),
            'isInstallment' => $order->downPayment()->exists(),
            'amountUnbilled' => $order->billing_summary['amount_unbilled'],
        ])->baseRoute('admin.order.show', $order->id);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderInvoiceRequest $request, InvoiceService $invoiceService, Order $order)
    {
        $invoice = $invoiceService->createFromOrder($order, array_merge($request->validated(), [
            'type' => $request->input('type', InvoiceType::FINAL_PAYMENT->value),
        ]));

        return to_route('admin.billing.show', $invoice->id)
            ->with('success', 'Billing created successfully.');
    }
}
