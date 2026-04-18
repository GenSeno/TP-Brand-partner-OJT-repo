<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
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

        // You can fetch collections data here if needed
        // $collections = BrandPartnerCollection::where('brand_partner_id', $brandPartner->id)->get();

        return Inertia::render('store/collections', [
            'brandPartner' => $brandPartner,
            // 'collections' => $collections ?? [],
        ]);
    }
}