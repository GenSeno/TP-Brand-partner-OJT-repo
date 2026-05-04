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

class BrandPartnerStoreController extends Controller
{
    /**
     * Display the brand partner store page.
     */
    public function index(Request $request)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $categories = $brandPartner->categories()
            ->enabled()
            ->ordered()
            ->withCount(['products' => fn ($q) => $q->published()])
            ->get();

        $events = $brandPartner->events()
            ->enabled()
            ->withCount(['products' => fn ($q) => $q->published()])
            ->get();

        $productsQuery = $brandPartner->products()
            ->published()
            ->with(['category', 'event', 'images', 'collection']);

        if ($request->filled('category')) {
            $productsQuery->where('category_id', $request->category);
        }

        if ($request->filled('event')) {
            $productsQuery->where('event_id', $request->event);
        }

        if ($request->boolean('featured')) {
            $productsQuery->featured();
        }

        if ($request->filled('search')) {
            $productsQuery->search($request->search);
        }

        $products = $productsQuery->latest()->paginate(8)->withQueryString();

        $featuredProducts = $brandPartner->products()
            ->published()
            ->featured()
            ->with(['category', 'event', 'images', 'collection'])
            ->latest()
            ->limit(4)
            ->get();

        return Inertia::render('store/index', [
            'brandPartner' => $brandPartner,
            'categories' => $categories,
            'events' => $events,
            'products' => $products,
            'featuredProducts' => $featuredProducts,
            'filter' => $request->only(['category', 'event', 'featured', 'search']),
            'wishlistedIds' => Auth::check()
                ? Wishlist::where('user_id', Auth::id())
                    ->pluck('brand_partner_product_id')
                    ->toArray()
                : [],
        ]);
    }

    /**
     * Display a product detail page.
     */
    public function product(Request $request, string $productSlug)
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $product = BrandPartnerProduct::where('brand_partner_id', $brandPartner->id)
            ->where('slug', $productSlug)
            ->where('status', BrandPartnerProductStatus::PUBLISHED)
            ->with(['category', 'event', 'images'])
            ->firstOrFail();

        $relatedProducts = BrandPartnerProduct::where('brand_partner_id', $brandPartner->id)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->published()
            ->with('images')
            ->limit(4)
            ->get();

        return Inertia::render('store/product', [
            'brandPartner' => $brandPartner,
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'wishlistedIds' => Auth::check()
                ? Wishlist::where('user_id', Auth::id())
                    ->pluck('brand_partner_product_id')
                    ->toArray()
                : [],
        ]);
    }
}
