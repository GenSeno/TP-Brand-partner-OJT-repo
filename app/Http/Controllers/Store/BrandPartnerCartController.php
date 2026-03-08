<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandPartnerCartController extends Controller
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
     * Save the cart to session.
     */
    protected function saveCart(Request $request, string $brandPartnerSlug, array $cart): void
    {
        $request->session()->put($this->getCartKey($brandPartnerSlug), $cart);
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

        foreach ($cart as $productId => $quantity) {
            $product = BrandPartnerProduct::with('images')
                ->where('id', $productId)
                ->where('brand_partner_id', $brandPartner->id)
                ->where('status', BrandPartnerProductStatus::PUBLISHED)
                ->first();

            if ($product) {
                $itemTotal = $product->price * $quantity;
                $cartItems[] = [
                    'id' => $product->id,
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'total' => $itemTotal,
                ];
                $total += $itemTotal;
            }
        }

        return Inertia::render('store/cart', [
            'brandPartner' => $brandPartner,
            'cart' => [
                'items' => $cartItems,
                'subtotal' => $total,
                'discount' => 0,
                'total' => $total,
            ],
            'cartCount' => array_sum($cart),
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $product = BrandPartnerProduct::where('id', $request->product_id)
            ->where('brand_partner_id', $brandPartner->id)
            ->where('status', BrandPartnerProductStatus::PUBLISHED)
            ->firstOrFail();

        // Check stock if tracking is enabled
        if ($product->track_stock && $product->stock < $request->quantity) {
            return back()->with('error', __('Insufficient stock available.'));
        }

        $cart = $this->getCart($request, $brandPartnerSlug);

        $currentQty = $cart[$product->id] ?? 0;
        $newQty = $currentQty + $request->quantity;

        // Check stock for total quantity
        if ($product->track_stock && $product->stock < $newQty) {
            return back()->with('error', __('Insufficient stock available.'));
        }

        $cart[$product->id] = $newQty;
        $this->saveCart($request, $brandPartnerSlug, $cart);

        return back()->with('success', __('Product added to cart.'));
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, int $itemId)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $product = BrandPartnerProduct::where('id', $itemId)
            ->where('brand_partner_id', $brandPartner->id)
            ->firstOrFail();

        // Check stock if tracking is enabled
        if ($product->track_stock && $product->stock < $request->quantity) {
            return back()->with('error', __('Insufficient stock available.'));
        }

        $cart = $this->getCart($request, $brandPartnerSlug);
        $cart[$itemId] = $request->quantity;
        $this->saveCart($request, $brandPartnerSlug, $cart);

        return back()->with('success', __('Cart updated.'));
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request, int $itemId)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');
        $cart = $this->getCart($request, $brandPartnerSlug);
        unset($cart[$itemId]);
        $this->saveCart($request, $brandPartnerSlug, $cart);

        return back()->with('success', __('Product removed from cart.'));
    }
}
