<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderShippingRequest;
use App\Lunar\DataTypes\Price;
use App\Lunar\ValueObjects\Cart\ShippingBreakdown;
use App\Lunar\ValueObjects\Cart\ShippingBreakdownItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderShippingController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Order $order)
    {
        $billingId = $request->query('billing');

        if ($billingId) {
            return Inertia::modal('admin/order/shipping/edit', [
                'order' => $order,
                'billingId' => $billingId,
            ])->baseRoute('admin.billing.show', $billingId);
        }

        return Inertia::modal('admin/order/shipping/edit', [
            'order' => $order,
            'billingId' => null,
        ])->baseRoute('admin.order.show', $order->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderShippingRequest $request, Order $order)
    {
        $billingId = $request->input('billing_id');
        $lines = $request->input('lines', []);

        $shipping = new ShippingBreakdown();

        foreach ($lines as $index => $line) {
            $shipping->items->add(
                new ShippingBreakdownItem(
                    $line['description'] ?? 'Add on Fee ' . ($index + 1),
                    'ADDON_' . ($index + 1),
                    new Price(
                        (int) round($line['amount'] * 100),
                        $order->currency
                    )
                )
            );
        }

        $shippingTotal = $shipping->items->sum(fn ($item) => $item->price->value);

        $order->update([
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
    public function destroy(Order $order)
    {
        $order->update([
            'shipping_breakdown' => null,
        ]);

        return to_route('admin.billing.show', $order->invoice->id)
            ->with('success', 'Add on fee removed successfully.');
    }
}
