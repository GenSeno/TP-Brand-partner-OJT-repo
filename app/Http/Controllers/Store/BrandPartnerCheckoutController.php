<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerOrderStatus;
use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerOrder;
use App\Models\BrandPartnerProduct;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BrandPartnerCheckoutController extends Controller
{
    /**
     * Get the cart session key for a brand partner.
     */
    protected function getCartKey(string $brandPartnerSlug): string
    {
        return "bp_cart_{$brandPartnerSlug}";
    }

    /**
     * Get the cart items from session.
     */
    protected function getCart(Request $request, string $brandPartnerSlug): array
    {
        return $request->session()->get($this->getCartKey($brandPartnerSlug), []);
    }

    /**
     * Clear the cart.
     */
    protected function clearCart(Request $request, string $brandPartnerSlug): void
    {
        $request->session()->forget($this->getCartKey($brandPartnerSlug));
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

        $cart = $this->getCart($request, $brandPartnerSlug);

        if (empty($cart)) {
            return redirect()->route('store.brand-partner.cart')
                ->with('error', __('Your cart is empty.'));
        }

        $cartItems = [];
        $subTotal = 0;

        foreach ($cart as $itemKey => $item) {
            $product = BrandPartnerProduct::with('images')
                ->where('id', $item['product_id'])
                ->where('brand_partner_id', $brandPartner->id)
                ->where('status', BrandPartnerProductStatus::PUBLISHED)
                ->first();

            if ($product) {
                $itemTotal = $product->price * $item['quantity'];
                $cartItems[] = [
                    'id'       => $itemKey,
                    'product'  => $product,
                    'color'    => $item['color'] ?? null,
                    'size'     => $item['size'] ?? null,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price,
                    'total'    => $itemTotal,
                ];
                $subTotal += $itemTotal;
            }
        }

        if (empty($cartItems)) {
            return redirect()->route('store.brand-partner.cart')
                ->with('error', __('Your cart is empty.'));
        }

        $countries = Country::orderBy('name')->get();
        $defaultCountryId = Country::where('iso2', 'PH')->value('id');

        return Inertia::render('store/checkout', [
            'brandPartner' => $brandPartner,
            'cart' => [
                'items' => $cartItems,
                'subtotal' => $subTotal,
                'discount' => 0,
                'total' => $subTotal,
            ],
            'cartCount' => array_sum(array_column($cart, 'quantity')),
            'countries' => $countries,
            'defaultCountryId' => $defaultCountryId,
        ]);
    }

    /**
     * Process the checkout.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'       => ['required', 'string', 'max:255'],
            'customer_email'      => ['required', 'email', 'max:255'],
            'customer_phone'      => ['nullable', 'string', 'max:50'],
            'shipping_line1'      => ['nullable', 'string', 'max:255'],
            'shipping_line2'      => ['nullable', 'string', 'max:255'],
            'shipping_province'   => ['nullable', 'string', 'max:255'],
            'shipping_city'       => ['nullable', 'string', 'max:255'],
            'shipping_barangay'   => ['nullable', 'string', 'max:255'],
            'shipping_postcode'   => ['nullable', 'string', 'max:20'],
            'shipping_country_id' => ['nullable', 'integer'],
            'notes'               => ['nullable', 'string'],
        ]);

        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $cart = $this->getCart($request, $brandPartnerSlug);

        if (empty($cart)) {
            return back()->with('error', __('Your cart is empty.'));
        }

        $order = DB::transaction(function () use ($request, $brandPartner, $cart) {
            $subTotal = 0;
            $orderLines = [];

            foreach ($cart as $itemKey => $item) {
                $product = BrandPartnerProduct::where('id', $item['product_id'])
                    ->where('brand_partner_id', $brandPartner->id)
                    ->where('status', BrandPartnerProductStatus::PUBLISHED)
                    ->first();

                if (!$product) {
                    continue;
                }

                $quantity = $item['quantity'];
                $color    = $item['color'] ?? null;
                $size     = $item['size'] ?? null;

                // Check stock
                if ($product->track_stock && $product->stock < $quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $itemTotal = $product->price * $quantity;
                $subTotal += $itemTotal;

                $meta = ($color || $size) ? ['color' => $color, 'size' => $size] : null;

                $orderLines[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'quantity'     => $quantity,
                    'unit_price'   => $product->price,
                    'total'        => $itemTotal,
                    'meta'         => $meta,
                ];

                $product->decrementStock($quantity);
            }

            if (empty($orderLines)) {
                throw new \Exception('No valid products in cart.');
            }

            // Create order
            $order = BrandPartnerOrder::create([
                'brand_partner_id' => $brandPartner->id,
                'customer_id' => auth()->id(), // null if guest
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'status' => BrandPartnerOrderStatus::PENDING,
                'sub_total' => $subTotal,
                'tax_total' => 0,
                'total' => $subTotal,
                'notes' => $request->notes,
                'placed_at' => now(),
                'meta' => [
                    'shipping_address' => [
                        'line1'      => $request->shipping_line1,
                        'line2'      => $request->shipping_line2,
                        'province'   => $request->shipping_province,
                        'city'       => $request->shipping_city,
                        'barangay'   => $request->shipping_barangay,
                        'postcode'   => $request->shipping_postcode,
                        'country_id' => $request->shipping_country_id,
                    ],
                ],
            ]);

            // Create order lines
            foreach ($orderLines as $line) {
                $order->lines()->create($line);
            }

            return $order;
        });

        // Clear cart
        $this->clearCart($request, $brandPartnerSlug);

        return redirect()->route('store.brand-partner.order.confirmation', [
            'reference' => $order->reference,
        ])->with('success', __('Order placed successfully!'));
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
