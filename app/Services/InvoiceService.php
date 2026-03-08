<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Address;
use App\Models\Invoice;
use App\Models\InvoiceAddress;
use App\Models\InvoiceLine;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Create an invoice from an order.
     *
     * @param Order $order
     * @param array $attributes Optional attributes to override defaults
     * @return Invoice
     */
    public function createFromOrder(Order $order, array $attributes = []): Invoice
    {
        return DB::transaction(function () use ($order, $attributes) {
            // Create the invoice
            $invoice = $this->createInvoice($order, $attributes);

            // Copy addresses from order
            $this->copyAddresses($order, $invoice);

            // Copy line items from order
            $this->copyLineItems($order, $invoice);

            return $invoice->load(['lines', 'billingAddress', 'shippingAddress']);
        });
    }

    /**
     * Create the main invoice record.
     *
     * @param Order $order
     * @param array $attributes
     * @return Invoice
     */
    protected function createInvoice(Order $order, array $attributes): Invoice
    {
        $order->append('billing_summary');
        $type = InvoiceType::from($attributes['type']);
        $amountUnbilled = $order->billing_summary['amount_unbilled']->decimal;
        if ($amountUnbilled <= 0) {
            throw new \Exception('No amount due to invoice.');
        }

        // Handle installment/final payment logic
        if ($type->is(InvoiceType::INSTALLMENT)) {
            if (!$order->downPayment()->exists()) {
                throw new \Exception('Down payment must exist before creating an installment invoice.');
            }
            if (!empty($attributes['amount_due']) && $attributes['amount_due'] >= $amountUnbilled) {
                $type = InvoiceType::FINAL_PAYMENT;
            }
        } elseif ($order->invoices()->whereType($type)->exists()) {
            throw new \Exception('Invoice of this type already exists for this order.');
        }

        // Set amount_due for final payment
        $amountDue = $type->is(InvoiceType::FINAL_PAYMENT)
            ? $amountUnbilled
            : ($attributes['amount_due'] ?? $amountUnbilled);
        $amountDueCents = (int) round($amountDue * 100);

        // VAT calculation (12%) --vat
        $vatableAmount = (int) round($amountDueCents / 1.12);
        $vatAmount = $amountDueCents - $vatableAmount;

        /** @var Address $billing */
        $billing = $order->billingAddress;
        $customerName = $attributes['customer_name'] ?? ($billing ? $billing->full_name : 'N/A');
        $customerEmail = $attributes['customer_email'] ?? ($billing ? $billing->email : 'N/A');

        return Invoice::create([
            'order_id' => $order->id,
            'invoiced_at' => $attributes['invoiced_at'] ?? now(),
            'type' => $type,
            'due_at' => $attributes['due_at'] ?? null,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'sub_total' => $order->sub_total->value,
            'total' => $order->total->value,
            'amount_due' => $amountDueCents,
            'vatable_amount' => $vatableAmount,
            'vat_amount' => $vatAmount,
            'status' => $attributes['status'] ?? InvoiceStatus::UNPAID,
        ]);
    }

    protected function createPayment(Invoice $invoice, array $attributes)
    {
        return $invoice->payments()->create([
            'billed_at' => $attributes['billed_at'] ?? now(),
            'amount' => $attributes['amount'] * 100,
            'due_at' => $attributes['due_at'],
            'notes' => $attributes['notes'] ?? null,
        ]);
    }

    public function resyncInvoiceFromOrder(Order $order)
    {
        if ($order->invoices()->doesntExist()) {
            throw new \Exception('No invoices found for this order.');

        }

        $orderTotal = $order->total->value;

        // Calculate vatable amount and VAT (assuming 12% VAT) --vat
        $vatableAmount = (int) round($orderTotal / 1.12);
        $vatAmount = $orderTotal - $vatableAmount;

        return $order->invoices()->status(
            InvoiceStatus::DRAFT,
            InvoiceStatus::UNPAID,
            InvoiceStatus::PARTIALLY_PAID
        )->update([
                    'sub_total' => $order->sub_total->value,
                    'total' => $orderTotal,
                    'vatable_amount' => $vatableAmount,
                    'vat_amount' => $vatAmount,
                ]);
    }

    /**
     * Copy addresses from order to invoice.
     *
     * @param Order $order
     * @param Invoice $invoice
     * @return void
     */
    protected function copyAddresses(Order $order, Invoice $invoice): void
    {
        $order->addresses->each(function ($address) use ($invoice, $order) {
            InvoiceAddress::create([
                'invoice_id' => $invoice->id,
                'country_id' => $address->country_id,
                'title' => $address->title,
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'company_name' => $address->company_name,
                'line1' => $address->line1,
                'line2' => $address->line2,
                'barangay' => $address->barangay,
                'city' => $address->city,
                'province' => $address->province,
                'postcode' => $address->postcode,
                'delivery_instructions' => $address->delivery_instructions,
                'email' => $address->email,
                'phone' => $address->phone,
                'type' => $address->type,
                'shipping_option' => $address->shipping_option,
                'meta' => $address->meta ? (array) $address->meta : null,
            ]);
        });
    }

    /**
     * Copy line items from order print lines to invoice.
     *
     * @param Order $order
     * @param Invoice $invoice
     * @return void
     */
    protected function copyLineItems(Order $order, Invoice $invoice): void
    {
        $order->printLines->each(function ($printLine, $index) use ($invoice) {
            InvoiceLine::create([
                'invoice_id' => $invoice->id,
                'product_id' => $printLine->product_id,
                'product_name' => $printLine->product_name,
                'sku' => $printLine->sku,
                'uom_code' => $printLine->uom_code,
                'unit_price' => $printLine->unit_price,
                'quantity' => $printLine->quantity,
                'total' => $printLine->total,
                'options_payload' => $printLine->options_payload ?? [],
                'sort_order' => $printLine->sort_order ?? ($index * 10),
            ]);
        });
    }
}
