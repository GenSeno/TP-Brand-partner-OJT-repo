<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Order;
use Inertia\Inertia;

class OrderAddressController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        return Inertia::modal('admin/order/address/edit', [
            'order' => $order->load(['billingAddress', 'shippingAddress', 'orderable']),
            'customers' => Customer::orderByName()->getOptions('full_name', 'id'),
            'countries' => Country::get(['id', 'name', 'emoji']),
        ])->baseRoute('admin.order.show', ['order' => $order->id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, Order $order)
    {
        $order->update([
            'notes' => $request->input('notes'),
            'status' => $request->input('status', $order->status),
        ]);

        // Update billing address
        if ($request->has('address')) {
            $billingData = $request->input('address');
            $billingData['type'] = 'billing';

            $billingAddress = $order->billingAddress()->first();
            if ($billingAddress) {
                $billingAddress->update($billingData);
            } else {
                $order->addresses()->create($billingData);
            }
        }

        // Update or create shipping address
        if ($request->has('shipping') && $request->input('shipping')) {
            // Different shipping address provided
            $shippingData = $request->input('shipping');
            $shippingData['type'] = 'shipping';

            $shippingAddress = $order->shippingAddress()->first();
            if ($shippingAddress) {
                $shippingAddress->update($shippingData);
            } else {
                $order->addresses()->create($shippingData);
            }
        } else {
            $order->shippingAddress()->delete();
        }

        return response()->json([
            'message' => __('crud.updated', ['record' => 'Sales Order']),
        ]);
    }
}
