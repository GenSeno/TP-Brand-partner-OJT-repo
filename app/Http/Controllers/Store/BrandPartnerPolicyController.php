<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use App\Models\Policy;
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

    private function renderPolicy(string $slug)
    {
        $policy = Policy::getBySlug($slug);

        if (! $policy) {
            abort(404);
        }

        return Inertia::render('store/policy', [
            'brandPartner' => $this->getBrandPartner(),
            'policy' => [
                'title' => $policy->title,
                'content' => $policy->content,
                'lastUpdatedDate' => $policy->last_updated_date,
            ],
        ]);
    }

    public function terms()
    {
        return $this->renderPolicy('terms');
    }

    public function privacy()
    {
        return $this->renderPolicy('privacy');
    }

    public function refund()
    {
        return $this->renderPolicy('refund');
    }

    public function shipping()
    {
        return $this->renderPolicy('shipping');
    }

    public function payment()
    {
        return $this->renderPolicy('payment');
    }

    public function cookies()
    {
        return $this->renderPolicy('cookies');
    }

    public function warranty()
    {
        return $this->renderPolicy('warranty');
    }

    public function cancellation()
    {
        return $this->renderPolicy('cancellation');
    }

    public function disclaimer()
    {
        return $this->renderPolicy('disclaimer');
    }

    public function acceptableUse()
    {
        return $this->renderPolicy('acceptable-use');
    }
}
