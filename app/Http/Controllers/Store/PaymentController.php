<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\BrandPartnerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class PaymentController extends Controller
{
    public function __construct()
    {
        Configuration::setXenditKey(config('services.xendit.secret_key'));
    }

    public function createInvoice(Request $request, string $orderReference)
    {
        $order = BrandPartnerOrder::where('reference', $orderReference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $invoiceApi = new InvoiceApi();

        $params = new CreateInvoiceRequest([
            'external_id'       => $order->reference,
            'amount'            => $order->total / 100,
            'payer_email'       => Auth::user()->email,
            'description'       => 'Order #' . $order->reference,
            'success_redirect_url' => route('store.payment.success', $order->reference),
            'failure_redirect_url' => route('store.payment.failed', $order->reference),
            'currency'          => 'PHP',
            'items'             => $order->lines->map(fn($line) => [
                'name'     => $line->product->name,
                'quantity' => $line->quantity,
                'price'    => $line->price / 100,
                'category' => 'APPAREL',
            ])->toArray(),
        ]);

        $invoice = $invoiceApi->createInvoice($params);

        $order->update([
            'payment_invoice_id' => $invoice['id'],
            'payment_status'     => 'pending',
        ]);

        return Inertia::location($invoice['invoice_url']);
    }

    public function success(Request $request, string $reference)
{
    $order = BrandPartnerOrder::where('reference', $reference)->firstOrFail();
    $order->update(['payment_status' => 'paid']);

    return redirect()->route('store.brand-partner.order.confirmation', [
        'reference' => $reference,
    ]);
}

    public function failed(Request $request, string $orderReference)
    {
        $order = BrandPartnerOrder::where('reference', $orderReference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return Inertia::render('store/payment-failed', [
            'order' => $order,
        ]);
    }

       /* public function failed(Request $request, string $reference)
        {
        $order = BrandPartnerOrder::where('reference', $reference)->firstOrFail();
        $order->update(['payment_status' => 'failed']);

        return redirect()->route('store.brand-partner.order.confirmation', [
            'reference' => $reference,
        ]);
        } */

    public function webhook(Request $request)
    {
        $webhookToken = $request->header('x-callback-token');
        if ($webhookToken !== config('services.xendit.webhook_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        $status  = $payload['status'] ?? null;
        $externalId = $payload['external_id'] ?? null;

        $order = BrandPartnerOrder::where('reference', $externalId)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($status === 'PAID') {
            $order->update(['payment_status' => 'paid']);
            // TODO: trigger order fulfillment here
        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            $order->update(['payment_status' => 'failed']);
        }

        return response()->json(['success' => true]);
    }
}