<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandPartnerStoreController extends Controller
{
    /**
     * Get cart count for a brand partner.
     */
    protected function getCartCount(Request $request, string $brandPartnerSlug): int
    {
        $cart = $request->session()->get("bp_cart_{$brandPartnerSlug}", []);
        return array_sum(array_column($cart, 'quantity'));
    }

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
            ->withCount(['products' => fn($q) => $q->published()])
            ->get();

        $events = $brandPartner->events()
            ->enabled()
            ->withCount(['products' => fn($q) => $q->published()])
            ->get();

        $productsQuery = $brandPartner->products()
            ->published()
            ->with(['category', 'event', 'images']);

        // Filter by category
        if ($request->filled('category')) {
            $productsQuery->where('category_id', $request->category);
        }

        // Filter by event
        if ($request->filled('event')) {
            $productsQuery->where('event_id', $request->event);
        }

        // Filter featured
        if ($request->boolean('featured')) {
            $productsQuery->featured();
        }

        // Search
        if ($request->filled('search')) {
            $productsQuery->search($request->search);
        }

        $products = $productsQuery->latest()->paginate(8)->withQueryString();

        $featuredProducts = $brandPartner->products()
            ->published()
            ->featured()
            ->with(['category', 'event', 'images'])
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
            'cartCount' => $this->getCartCount($request, $brandPartnerSlug),
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

        // Get related products from the same category
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
            'cartCount' => $this->getCartCount($request, $brandPartnerSlug),
        ]);
    }
}
