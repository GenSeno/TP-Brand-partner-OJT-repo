<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use Inertia\Inertia;

class BrandPartnerPartnerController extends Controller
{
    /**
     * Display the Be Our Partner page.
     */
    public function index()
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        return Inertia::render('store/partner', [
            'brandPartner' => $brandPartner,
        ]);
    }
}
