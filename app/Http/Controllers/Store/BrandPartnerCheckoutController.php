<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerOrderStatus;
use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerOrder;
use App\Models\BrandPartnerProduct;
use App\Models\CartItem;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BrandPartnerCheckoutController extends Controller
{
    protected function getCartKey(string $brandPartnerSlug): string
    {
        return "bp_cart_{$brandPartnerSlug}";
    }

    protected function getSessionCart(Request $request, string $brandPartnerSlug): array
    {
        return $request->session()->get($this->getCartKey($brandPartnerSlug), []);
    }

    protected function clearCart(Request $request, string $brandPartnerSlug): void
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            $request->session()->forget($this->getCartKey($brandPartnerSlug));
        }
    }

    /**
     * Build cart items array from DB (logged in) or session (guest).
     */
    protected function resolveCart(Request $request, BrandPartner $brandPartner): array
    {
        $cartItems = [];
        $subTotal = 0;

        if (Auth::check()) {
            // Logged in: load from DB
            $dbItems = CartItem::with(['product.images'])
                ->whereHas('product', function ($q) use ($brandPartner) {
                    $q->where('brand_partner_id', $brandPartner->id)
                        ->where('status', BrandPartnerProductStatus::PUBLISHED);
                })
                ->where('user_id', Auth::id())
                ->get();

            foreach ($dbItems as $item) {
                $itemTotal = $item->product->price * $item->quantity;
                $subTotal += $itemTotal;
                $cartItems[] = [
                    'id' => $item->id,
                    'product_id' => $item->brand_partner_product_id,
                    'product' => $item->product,
                    'color' => $item->color,
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total' => $itemTotal,
                ];
            }
        } else {
            // Guest: load from session
            $sessionCart = $this->getSessionCart($request, config('store.brand_partner_slug'));

            foreach ($sessionCart as $itemKey => $item) {
                $product = BrandPartnerProduct::with('images')
                    ->where('id', $item['product_id'])
                    ->where('brand_partner_id', $brandPartner->id)
                    ->where('status', BrandPartnerProductStatus::PUBLISHED)
                    ->first();

                if (!$product) {
                    continue;
                }

                $itemTotal = $product->price * $item['quantity'];
                $subTotal += $itemTotal;
                $cartItems[] = [
                    'id' => $itemKey,
                    'product_id' => $item['product_id'],
                    'product' => $product,
                    'color' => $item['color'] ?? null,
                    'size' => $item['size'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $itemTotal,
                ];
            }
        }

        return [$cartItems, $subTotal];
    }

    /**
     * Display the checkout page.
     */
    public function index(Request $request)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        [$cartItems, $subTotal] = $this->resolveCart($request, $brandPartner);

        if (empty($cartItems)) {
            return redirect()->route('store.brand-partner.cart')
                ->with('error', __('Your cart is empty.'));
        }

        $countries = Country::orderBy('name')->get();
        $defaultCountryId = Country::where('iso2', 'PH')->value('id');

        $userAddresses = [];
        if (Auth::check()) {
            $userAddresses = Auth::user()->load('addresses.country')->addresses;
        }

        return Inertia::render('store/checkout', [
            'brandPartner' => $brandPartner,
            'cart' => [
                'items' => $cartItems,
                'subtotal' => $subTotal,
                'discount' => 0,
                'total' => $subTotal,
            ],
            'cartCount' => array_sum(array_column($cartItems, 'quantity')),
            'countries' => $countries,
            'defaultCountryId' => $defaultCountryId,
            'userAddresses' => $userAddresses,
        ]);
    }

    /**
     * Process the checkout.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'shipping_line1' => ['nullable', 'string', 'max:255'],
            'shipping_line2' => ['nullable', 'string', 'max:255'],
            'shipping_province' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['nullable', 'string', 'max:255'],
            'shipping_barangay' => ['nullable', 'string', 'max:255'],
            'shipping_postcode' => ['nullable', 'string', 'max:20'],
            'shipping_country_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        [$cartItems, $subTotal] = $this->resolveCart($request, $brandPartner);

        if (empty($cartItems)) {
            return back()->with('error', __('Your cart is empty.'));
        }

        $order = DB::transaction(function () use ($request, $brandPartner, $cartItems, $subTotal) {
            $orderLines = [];

            foreach ($cartItems as $item) {
                $product = BrandPartnerProduct::where('id', $item['product_id'])
                    ->where('brand_partner_id', $brandPartner->id)
                    ->where('status', BrandPartnerProductStatus::PUBLISHED)
                    ->first();

                if (!$product) {
                    continue;
                }

                $quantity = $item['quantity'];

                $isPreOrder = false;
                if ($product->meta && isset($product->meta['variants'])) {
                    $match = collect($product->meta['variants'])->firstWhere(
                        fn($v) => (!$item['color'] || $v['color'] === $item['color']) &&
                        (!$item['size'] || $v['size'] === $item['size'])
                    );
                    $isPreOrder = $match && ($match['stock'] ?? 0) < $quantity;
                }

                $meta = array_merge(
                    ($item['color'] || $item['size']) ? ['color' => $item['color'], 'size' => $item['size']] : [],
                    $isPreOrder ? ['pre_order' => true] : [],
                );
                $meta = empty($meta) ? null : $meta;

                $orderLines[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total' => $product->price * $quantity,
                    'meta' => $meta,
                ];

                $product->decrementStock($quantity, $item['color'] ?? null, $item['size'] ?? null);
            }

            if (empty($orderLines)) {
                throw new \Exception('No valid products in cart.');
            }

            $order = BrandPartnerOrder::create([
                'brand_partner_id' => $brandPartner->id,
                'customer_id' => Auth::id(),
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'status' => BrandPartnerOrderStatus::PENDING,
                'sub_total' => $subTotal,
                'tax_total' => 0,
                'total' => $subTotal,
                'notes' => $request->notes,
                'placed_at' => now(),
                'address_line1' => $request->shipping_line1,
                'address_line2' => $request->shipping_line2,
                'barangay' => $request->shipping_barangay,
                'city' => $request->shipping_city,
                'province' => $request->shipping_province,
                'postcode' => $request->shipping_postcode,
            ]);

            foreach ($orderLines as $line) {
                $order->lines()->create($line);
            }

            return $order;
        });

        $this->clearCart($request, $brandPartnerSlug);

        try {
            $successUrl = route('store.payment.success', $order->reference);
            $failureUrl = route('store.payment.failed', $order->reference);

            $response = \Illuminate\Support\Facades\Http::withBasicAuth(
                config('services.xendit.secret_key'),
                ''
            )->post('https://api.xendit.co/v2/invoices', [
                        'external_id' => $order->reference,
                        'amount' => $order->total / 100,
                        'payer_email' => $order->customer_email,
                        'description' => 'Order #' . $order->reference,
                        'success_redirect_url' => $successUrl,
                        'failure_redirect_url' => $failureUrl,
                        'currency' => 'PHP',
                    ]);

            if ($response->failed()) {
                throw new \Exception('Xendit API error: ' . $response->body());
            }

            $invoice = $response->json();

            $order->update([
                'payment_invoice_id' => $invoice['id'],
                'payment_status' => 'pending',
            ]);

            return Inertia::location($invoice['invoice_url']);

        } catch (\Exception $e) {
            ('Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the order confirmation page.
     */
    public function confirmation(Request $request, string $reference)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $order = BrandPartnerOrder::where('brand_partner_id', $brandPartner->id)
            ->where('reference', $reference)
            ->with('lines.product')
            ->firstOrFail();

        return Inertia::render('store/confirmation', [
            'brandPartner' => $brandPartner,
            'order' => $order,
        ]);
    }
}
