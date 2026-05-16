<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerProduct;
use App\Models\BrandPartnerProductOption;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $categoryOption = BrandPartnerProductOption::where('brand_partner_id', $brandPartner->id)
            ->where('name', 'Category')
            ->first();

        $categoryCounts = BrandPartnerProduct::published()
            ->where('brand_partner_id', $brandPartner->id)
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $categories = $categoryOption
            ? $categoryOption->values()
                ->get()
                ->map(fn ($value) => [
                    'id' => $value->id,
                    'label' => $value->label,
                    'value' => $value->value,
                    'products_count' => $categoryCounts->get($value->id, 0),
                ])
                ->all()
            : [];

        $events = $brandPartner->events()
            ->enabled()
            ->withCount(['products' => fn ($q) => $q->published()])
            ->get();

        $productsQuery = $brandPartner->products()
            ->published()
            ->with(['category', 'event', 'images', 'collection']);

        // Filter by category
        if ($request->filled('category')) {
            $categoryIds = is_array($request->category)
                ? $request->category
                : [$request->category];
            $productsQuery->whereIn('category_id', $categoryIds);
        }

        // Filter by event
        if ($request->filled('event')) {
            $productsQuery->where('event_id', $request->event);
        }

        // Filter by collection
        if ($request->filled('collection')) {
            $collectionIds = is_array($request->collection)
                ? $request->collection
                : [$request->collection];
            $productsQuery->whereIn('collection_id', $collectionIds);
        }

        // Filter by colors
        if ($request->filled('colors')) {
            foreach ((array) $request->colors as $color) {
                $productsQuery->where('colors', 'like', "%{$color}%");
            }
        }

        // Filter by sizes
        if ($request->filled('sizes')) {
            foreach ((array) $request->sizes as $size) {
                $productsQuery->where('sizes', 'like', "%{$size}%");
            }
        }

        // Filter featured
        if ($request->boolean('featured')) {
            $productsQuery->featured();
        }

        // Search
        if ($request->filled('search')) {
            $productsQuery->search($request->search);
        }

        // Filter by price range
        if ($request->filled('price_max')) {
            $productsQuery->where('price', '<=', $request->price_max * 100);
        }

        $products = $productsQuery->latest()->paginate(12)->withQueryString();

        $availableColors = BrandPartnerProduct::published()
            ->where('brand_partner_id', $brandPartner->id)
            ->whereNotNull('colors')
            ->pluck('colors')
            ->flatMap(fn ($c) => array_map('trim', explode(',', $c)))
            ->unique()
            ->values()
            ->sort()
            ->values();

        $sizeOption = BrandPartnerProductOption::where('brand_partner_id', $brandPartner->id)
            ->where('name', 'Size')
            ->first();

        $availableSizes = $sizeOption ? $sizeOption->values()->orderBy('position')->pluck('value') : collect();

        $collectionOption = BrandPartnerProductOption::where('brand_partner_id', $brandPartner->id)
            ->where('name', 'Collection')
            ->first();

        $collections = $collectionOption ? $collectionOption->values()->orderBy('position')->get() : collect();

        return Inertia::render('store/shop', [
            'brandPartner' => $brandPartner,
            'categories' => $categories,
            'events' => $events,
            'products' => $products,
            'collections' => $collections,
            'colors' => $availableColors->all(),
            'sizes' => $availableSizes->all(),
            'filter' => $request->only(['category', 'event', 'featured', 'search', 'collection', 'colors', 'sizes', 'price_max']),
            'cartCount' => $this->getCartCount($request, $brandPartnerSlug),
            'wishlistedIds' => Auth::check()
                ? Wishlist::where('user_id', Auth::id())
                    ->pluck('brand_partner_product_id')
                    ->toArray()
                : [],
        ]);
    }
}
