<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use Inertia\Inertia;

class BrandPartnerAboutController extends Controller
{
    /**
     * Display the about us page.
     */
    public function index()
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        return Inertia::render('store/about', [
            'brandPartner' => $brandPartner,
        ]);
    }
}