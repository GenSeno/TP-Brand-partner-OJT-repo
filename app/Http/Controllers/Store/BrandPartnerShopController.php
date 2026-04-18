<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerProductStatus;
use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\BrandPartnerProduct;
use App\Models\BrandPartnerProductOption;
use App\Models\BrandPartnerProductOptionValue;
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
            ->withCount(['products' => fn($q) => $q->published()])
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

        $products = $productsQuery->latest()->paginate(12)->withQueryString();

        $allPublishedProducts = $brandPartner->products()
            ->published()
            ->get(['colors', 'sizes']);

        $availableColors = $allPublishedProducts
            ->flatMap(fn ($product) => explode(',', $product->colors ?? ''))
            ->map(fn ($color) => trim($color))
            ->filter()
            ->unique()
            ->values();

        $availableSizes = $allPublishedProducts
            ->flatMap(fn ($product) => explode(',', $product->sizes ?? ''))
            ->map(fn ($size) => trim($size))
            ->filter()
            ->unique()
            ->values();

        $collectionIds = $brandPartner->products()
            ->published()
            ->whereNotNull('collection_id')
            ->pluck('collection_id')
            ->unique()
            ->filter()
            ->values();

        $collections = BrandPartnerProductOptionValue::whereIn('id', $collectionIds)
            ->get();

        return Inertia::render('store/shop', [  // This points to your shop.vue
            'brandPartner' => $brandPartner,
            'categories' => $categories,
            'events' => $events,
            'products' => $products,
            'collections' => $collections,
            'colors' => $availableColors->all(),
            'sizes' => $availableSizes->all(),
            'filter' => $request->only(['category', 'event', 'featured', 'search', 'collection', 'colors', 'sizes']),
            'cartCount' => $this->getCartCount($request, $brandPartnerSlug),
        ]);
    }
}