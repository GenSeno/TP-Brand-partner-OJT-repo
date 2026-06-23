<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use Inertia\Inertia;

class BrandPartnerPolicyController extends Controller
{
    private function getBrandPartner(): ?BrandPartner
    {
        $slug = config('store.brand_partner_slug');
        if (! $slug) {
            return null;
        }

        return BrandPartner::where('slug', $slug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->first();
    }

    private function renderPolicy(string $page)
    {
        return Inertia::render($page, [
            'brandPartner' => $this->getBrandPartner(),
        ]);
    }

    public function terms()
    {
        return $this->renderPolicy('store/policies/terms');
    }

    public function privacy()
    {
        return $this->renderPolicy('store/policies/privacy');
    }

    public function refund()
    {
        return $this->renderPolicy('store/policies/refund');
    }

    public function shipping()
    {
        return $this->renderPolicy('store/policies/shipping');
    }

    public function payment()
    {
        return $this->renderPolicy('store/policies/payment');
    }

    public function cookies()
    {
        return $this->renderPolicy('store/policies/cookies');
    }

    public function warranty()
    {
        return $this->renderPolicy('store/policies/warranty');
    }

    public function cancellation()
    {
        return $this->renderPolicy('store/policies/cancellation');
    }

    public function disclaimer()
    {
        return $this->renderPolicy('store/policies/disclaimer');
    }

    public function acceptableUse()
    {
        return $this->renderPolicy('store/policies/acceptable-use');
    }
}
