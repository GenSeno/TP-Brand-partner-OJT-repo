<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleStoreInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'store';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $navCollections = [];
        if ($brandPartnerSlug = config('store.brand_partner_slug')) {
            $brandPartner = \App\Models\BrandPartner::where('slug', $brandPartnerSlug)->first();
            if ($brandPartner) {
                // Find the option named "Collection"
                $collectionOption = \App\Models\BrandPartnerProductOption::where('brand_partner_id', $brandPartner->id)
                    ->where('name', 'Collection')
                    ->first();
                if ($collectionOption) {
                    $navCollections = \App\Models\BrandPartnerProductOptionValue::where('product_option_id', $collectionOption->id)
                        ->get();
                }
            }
        }

        return [
            ...parent::share($request),
            'theme' => 'food',
            'brandPartnerSlug' => $brandPartnerSlug,
            'navCollections' => $navCollections,
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
        ];
    }
}
