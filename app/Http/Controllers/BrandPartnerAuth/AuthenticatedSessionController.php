<?php

namespace App\Http\Controllers\BrandPartnerAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('admin/auth/login', [
            'canResetPassword' => Route::has('brand-partner.password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse|\Illuminate\Http\Response
    {
        $request->authenticate('brand_partner');

        $request->session()->regenerate();

        return Inertia::location(route('brand-partner.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse|\Illuminate\Http\Response
    {
        Auth::guard('brand_partner')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Inertia::location('/admin/login');
    }
}
