<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\StoreCollectionItem;
use Inertia\Inertia;

class BrandPartnerCollectionController extends Controller
{
    /**
     * Display the collections page.
     */
    public function index()
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $collectionItems = StoreCollectionItem::where('brand_partner_id', $brandPartner->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($item) => array_merge($item->toArray(), [
                'image' => $item->image ? asset('storage/'.$item->image) : null,
            ]));

        return Inertia::render('store/collections', [
            'brandPartner' => $brandPartner,
            'collectionItems' => $collectionItems,
        ]);
    }
}
