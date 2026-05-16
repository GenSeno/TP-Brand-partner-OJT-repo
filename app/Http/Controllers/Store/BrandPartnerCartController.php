<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerProduct;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BrandPartnerCartController extends Controller
{
    protected function getCartKey(string $brandPartnerSlug): string
    {
        return "bp_cart_{$brandPartnerSlug}";
    }

    protected function getSessionCart(Request $request, string $brandPartnerSlug): array
    {
        return $request->session()->get($this->getCartKey($brandPartnerSlug), []);
    }

    protected function saveSessionCart(Request $request, string $brandPartnerSlug, array $cart): void
    {
        $request->session()->put($this->getCartKey($brandPartnerSlug), $cart);
    }

    protected function buildItemKey(int $productId, ?string $color, ?string $size): string
    {
        $c = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($color ?? '')));
        $s = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($size ?? '')));

        return "{$productId}_{$c}_{$s}";
    }

    public function mergeSessionCartIntoDb(Request $request, int $userId): void
    {
        $brandPartnerSlug = config('store.brand_partner_slug');
        $sessionCart = $this->getSessionCart($request, $brandPartnerSlug);

        if (empty($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $item) {
            $existing = CartItem::where('user_id', $userId)
                ->where('brand_partner_product_id', $item['product_id'])
                ->where('color', $item['color'])
                ->where('size', $item['size'])
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item['quantity']);
            } else {
                CartItem::create([
                    'user_id' => $userId,
                    'brand_partner_product_id' => $item['product_id'],
                    'color' => $item['color'],
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        $this->saveSessionCart($request, $brandPartnerSlug, []);
    }

    public function index(Request $request)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $cartItems = [];
        $total = 0;

        if (Auth::check()) {
            $dbItems = CartItem::with(['product.images', 'product.collection'])
                ->whereHas('product', function ($q) use ($brandPartner) {
                    $q->where('brand_partner_id', $brandPartner->id)
                        ->where('status', BrandPartnerProductStatus::PUBLISHED);
                })
                ->where('user_id', Auth::id())
                ->get();

            foreach ($dbItems as $item) {
                $itemTotal = $item->product->price * $item->quantity;
                $total += $itemTotal;
                $cartItems[] = [
                    'id' => $item->id,
                    'product' => $item->product,
                    'color' => $item->color,
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total' => $itemTotal,
                ];
            }
        } else {
            $sessionCart = $this->getSessionCart($request, $brandPartnerSlug);

            foreach ($sessionCart as $itemKey => $item) {
                $product = BrandPartnerProduct::with(['images', 'collection'])
                    ->where('id', $item['product_id'])
                    ->where('brand_partner_id', $brandPartner->id)
                    ->where('status', BrandPartnerProductStatus::PUBLISHED)
                    ->first();

                if (! $product) {
                    continue;
                }

                $itemTotal = $product->price * $item['quantity'];
                $total += $itemTotal;
                $cartItems[] = [
                    'id' => $itemKey,
                    'product' => $product,
                    'color' => $item['color'] ?? null,
                    'size' => $item['size'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $itemTotal,
                ];
            }
        }

        $cartCount = array_sum(array_column($cartItems, 'quantity'));

        return Inertia::render('store/cart', [
            'brandPartner' => $brandPartner,
            'cart' => [
                'items' => $cartItems,
                'subtotal' => $total,
                'discount' => 0,
                'total' => $total,
            ],
            'cartCount' => $cartCount,
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
            'color' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:50'],
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
        $size = $request->size ?: null;

        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('brand_partner_product_id', $product->id)
                ->where('color', $color)
                ->where('size', $size)
                ->first();

            $newQty = ($cartItem ? $cartItem->quantity : 0) + $request->quantity;

            CartItem::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'brand_partner_product_id' => $product->id,
                    'color' => $color,
                    'size' => $size,
                ],
                ['quantity' => $newQty]
            );
        } else {
            $itemKey = $this->buildItemKey($product->id, $color, $size);
            $cart = $this->getSessionCart($request, $brandPartnerSlug);

            $currentQty = $cart[$itemKey]['quantity'] ?? 0;
            $newQty = $currentQty + $request->quantity;

            $cart[$itemKey] = [
                'product_id' => $product->id,
                'color' => $color,
                'size' => $size,
                'quantity' => $newQty,
            ];

            $this->saveSessionCart($request, $brandPartnerSlug, $cart);
        }

        return back()->with('success', __('Product added to cart.'));
    }

    public function update(Request $request, string $itemId)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $brandPartnerSlug = config('store.brand_partner_slug');

        if (Auth::check()) {
            $cartItem = CartItem::where('id', $itemId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $cartItem->update(['quantity' => $request->quantity]);
        } else {
            $cart = $this->getSessionCart($request, $brandPartnerSlug);

            if (! isset($cart[$itemId])) {
                return back()->with('error', __('Cart item not found.'));
            }

            $cart[$itemId]['quantity'] = $request->quantity;
            $this->saveSessionCart($request, $brandPartnerSlug, $cart);
        }

        return back()->with('success', __('Cart updated.'));
    }

    public function remove(Request $request, string $itemId)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        if (Auth::check()) {
            CartItem::where('id', $itemId)
                ->where('user_id', Auth::id())
                ->delete();
        } else {
            $cart = $this->getSessionCart($request, $brandPartnerSlug);
            unset($cart[$itemId]);
            $this->saveSessionCart($request, $brandPartnerSlug, $cart);
        }

        return back()->with('success', __('Product removed from cart.'));
    }

    public function clear(Request $request)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            $this->saveSessionCart($request, $brandPartnerSlug, []);
        }

        return back()->with('success', __('Cart cleared.'));
    }
}
