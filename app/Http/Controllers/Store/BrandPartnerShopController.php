<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandPartnerShopController extends Controller
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
     * Display the shop page.
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

        $products = $productsQuery->latest()->paginate(12)->withQueryString();

        return Inertia::render('store/shop', [  // This points to your shop.vue
            'brandPartner' => $brandPartner,
            'categories' => $categories,
            'events' => $events,
            'products' => $products,
            'filter' => $request->only(['category', 'event', 'featured', 'search']),
            'cartCount' => $this->getCartCount($request, $brandPartnerSlug),
        ]);
    }
}