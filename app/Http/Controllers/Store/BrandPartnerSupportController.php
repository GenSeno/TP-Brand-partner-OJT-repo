<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandPartnerSupportController extends Controller
{
    protected function getBrandPartner(): BrandPartner
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        return BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();
    }

    public function help()
    {
        return Inertia::render('store/support/HelpCenter', [
            'brandPartner' => $this->getBrandPartner(),
        ]);
    }

    public function supportTicket()
    {
        return Inertia::render('store/support/SupportTicket', [
            'brandPartner' => $this->getBrandPartner(),
        ]);
    }

    public function supportTicketSubmit(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'category' => ['required', 'string'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'order_number' => ['nullable', 'string', 'max:255'],
        ]);

        return back()->with('success', 'Your support ticket has been submitted. We will get back to you within 24-48 hours.');
    }

    public function liveChat()
    {
        return Inertia::render('store/support/LiveChat', [
            'brandPartner' => $this->getBrandPartner(),
        ]);
    }

    public function returns()
    {
        return Inertia::render('store/support/Returns', [
            'brandPartner' => $this->getBrandPartner(),
        ]);
    }

    public function returnsSubmit(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'order_number' => ['required', 'string', 'max:255'],
            'reason' => ['required', 'string'],
            'items' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
        ]);

        return back()->with('success', 'Your return request has been submitted. We will send you return instructions within 1-2 business days.');
    }

    public function warrantyClaims()
    {
        return Inertia::render('store/support/WarrantyClaims', [
            'brandPartner' => $this->getBrandPartner(),
        ]);
    }

    public function warrantyClaimsSubmit(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'order_number' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'defect_type' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        return back()->with('success', 'Your warranty claim has been submitted. Our team will review it and contact you within 2-3 business days.');
    }

    public function storeLocator()
    {
        return Inertia::render('store/support/StoreLocator', [
            'brandPartner' => $this->getBrandPartner(),
        ]);
    }
}
