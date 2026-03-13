<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BrandPartnerCartController extends Controller
{
    protected function getCartKey(string $brandPartnerSlug): string
    {
        return "bp_cart_{$brandPartnerSlug}";
    }

    protected function getCart(Request $request, string $brandPartnerSlug): array
    {
        return $request->session()->get($this->getCartKey($brandPartnerSlug), []);
    }

    protected function saveCart(Request $request, string $brandPartnerSlug, array $cart): void
    {
        $request->session()->put($this->getCartKey($brandPartnerSlug), $cart);
    }

    /**
     * Cart structure: { itemKey => {product_id, color, size, quantity} }
     * itemKey is URL-safe: e.g. "5_red_l", "5__" for no color/size.
     */
    protected function buildItemKey(int $productId, ?string $color, ?string $size): string
    {
        $c = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($color ?? '')));
        $s = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($size ?? '')));
        return "{$productId}_{$c}_{$s}";
    }

    protected function cartTotal(array $cart): int
    {
        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Display the cart page.
     */
    public function index(Request $request)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $cart = $this->getCart($request, $brandPartnerSlug);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $itemKey => $item) {
            $product = BrandPartnerProduct::with('images')
                ->where('id', $item['product_id'])
                ->where('brand_partner_id', $brandPartner->id)
                ->where('status', BrandPartnerProductStatus::PUBLISHED)
                ->first();

            if (!$product) {
                continue;
            }

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
            $total += $itemTotal;
        }

        return Inertia::render('store/cart', [
            'brandPartner' => $brandPartner,
            'cart'         => [
                'items'    => $cartItems,
                'subtotal' => $total,
                'discount' => 0,
                'total'    => $total,
            ],
            'cartCount' => $this->cartTotal($cart),
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'color'      => ['nullable', 'string', 'max:100'],
            'size'       => ['nullable', 'string', 'max:50'],
        ]);

        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $product = BrandPartnerProduct::where('id', $request->product_id)
            ->where('brand_partner_id', $brandPartner->id)
            ->where('status', BrandPartnerProductStatus::PUBLISHED)
            ->firstOrFail();

        $color = $request->color ?: null;
        $size  = $request->size ?: null;

        $itemKey = $this->buildItemKey($product->id, $color, $size);
        $cart = $this->getCart($request, $brandPartnerSlug);

        $currentQty = $cart[$itemKey]['quantity'] ?? 0;
        $newQty = $currentQty + $request->quantity;

        if ($product->track_stock && $product->stock < $newQty) {
            return back()->with('error', __('Insufficient stock available.'));
        }

        $cart[$itemKey] = [
            'product_id' => $product->id,
            'color'      => $color,
            'size'       => $size,
            'quantity'   => $newQty,
        ];

        $this->saveCart($request, $brandPartnerSlug, $cart);

        return back()->with('success', __('Product added to cart.'));
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, string $itemId)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $brandPartnerSlug = config('store.brand_partner_slug');
        $cart = $this->getCart($request, $brandPartnerSlug);

        if (!isset($cart[$itemId])) {
            return back()->with('error', __('Cart item not found.'));
        }

        $cart[$itemId]['quantity'] = $request->quantity;
        $this->saveCart($request, $brandPartnerSlug, $cart);

        return back()->with('success', __('Cart updated.'));
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request, string $itemId)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');
        $cart = $this->getCart($request, $brandPartnerSlug);
        unset($cart[$itemId]);
        $this->saveCart($request, $brandPartnerSlug, $cart);

        return back()->with('success', __('Product removed from cart.'));
    }

    /**
     * Clear the entire cart.
     */
    public function clear(Request $request)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');
        $this->saveCart($request, $brandPartnerSlug, []);

        return back()->with('success', __('Cart cleared.'));
    }
}
