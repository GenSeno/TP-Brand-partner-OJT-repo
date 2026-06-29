<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\Faq;
use Inertia\Inertia;

class BrandPartnerFaqController extends Controller
{
    public function index()
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $faqs = Faq::getActive()->get(['question', 'answer']);

        return Inertia::render('store/faq', [
            'brandPartner' => $brandPartner,
            'faqs' => $faqs,
        ]);
    }
}
