<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerProduct;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BrandPartnerWishlistController extends Controller
{
    /**
     * Display the wishlist page.
     */
    public function index(Request $request)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $items = Wishlist::with(['product.images'])
            ->whereHas('product', function ($q) use ($brandPartner) {
                $q->where('brand_partner_id', $brandPartner->id)
                  ->where('status', BrandPartnerProductStatus::PUBLISHED);
            })
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(fn($w) => [
                'id'      => $w->id,
                'product' => $w->product,
            ]);

        return Inertia::render('store/wishlist', [
            'brandPartner' => $brandPartner,
            'items'        => $items,
        ]);
    }

    /**
     * Toggle a product in the wishlist (add if not present, remove if present).
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('brand_partner_product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', __('Removed from wishlist.'));
        }

        Wishlist::create([
            'user_id'                  => Auth::id(),
            'brand_partner_product_id' => $request->product_id,
        ]);

        return back()->with('success', __('Added to wishlist.'));
    }

    /**
     * Remove a specific item from the wishlist.
     */
    public function remove(Request $request, int $itemId)
    {
        Wishlist::where('id', $itemId)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', __('Removed from wishlist.'));
    }
}