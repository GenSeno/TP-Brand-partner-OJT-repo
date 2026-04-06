<?php

namespace App\Http\Controllers\Store;

use App\Enums\BrandPartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandPartnerContactController extends Controller
{
    /**
     * Display the contact us page.
     */
    public function index()
    {
        $brandPartnerSlug = config('store.brand_partner_slug');

        $brandPartner = BrandPartner::where('slug', $brandPartnerSlug)
            ->where('status', BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        return Inertia::render('store/contact', [
            'brandPartner' => $brandPartner,
        ]);
    }

    /**
     * Handle contact form submission (optional).
     */
    public function submit(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $brandPartnerSlug = config('store.brand_partner_slug');

        // Handle form submission (send email, save to database, etc.)

        return back()->with('success', __('Thank you for your message. We will get back to you soon.'));
    }
}