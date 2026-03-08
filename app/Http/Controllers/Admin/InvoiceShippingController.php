<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderShippingRequest;
use App\Lunar\DataTypes\Price;
use App\Lunar\ValueObjects\Cart\ShippingBreakdown;
use App\Lunar\ValueObjects\Cart\ShippingBreakdownItem;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceShippingController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {   

         return Inertia::modal('admin/invoice/shipping/edit', [
            'invoice' => $invoice,
            'billingId' => $invoice->id,
        ])->baseRoute('admin.billing.show', $invoice->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderShippingRequest $request, Invoice $invoice)
    {
        $lines = $request->input('lines', []);

        $shipping = new ShippingBreakdown();

        foreach ($lines as $index => $line) {
            $shipping->items->add(
                new ShippingBreakdownItem(
                    $line['description'] ?? 'Add on Fee ' . ($index + 1),
                    'ADDON_' . ($index + 1),
                    new Price(
                        (int) round($line['amount'] * 100),
                        $invoice->currency
                    )
                )
            );
        }

        $shippingTotal = $shipping->items->sum(fn ($item) => $item->price->value);
        $invoice->update([
            'shipping_breakdown' => $shipping,
            'shipping_total' => $shippingTotal,
        ]);

        return response()->json([
            'message' => 'Add on fee saved successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->update([
            'shipping_breakdown' => null,
        ]);

        return to_route('admin.billing.show', $invoice->id )
            ->with('success', 'Add on fee removed successfully.');
    }
}
