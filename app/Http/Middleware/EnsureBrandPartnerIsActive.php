<?php

namespace App\Http\Middleware;

use App\Enums\BrandPartnerStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureBrandPartnerIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response|InertiaResponse
    {
        $brandPartner = Auth::guard('brand_partner')->user();

        if ($brandPartner && $brandPartner->status !== BrandPartnerStatus::ACTIVE) {
            Auth::guard('brand_partner')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('brand-partner.login')
                ->with('error', 'Your account is not active. Please contact support.');
        }

        return $next($request);
    }
}
