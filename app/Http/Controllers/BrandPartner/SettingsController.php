<?php

namespace App\Http\Controllers\BrandPartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Get the authenticated brand partner.
     */
    protected function brandPartner()
    {
        return Auth::guard('brand_partner')->user();
    }

    /**
     * Display the settings page.
     */
    public function index()
    {
        return Inertia::render('settings/index', [
            'brandPartner' => $this->brandPartner(),
        ]);
    }

    /**
     * Update the brand partner profile.
     */
    public function update(Request $request)
    {
        $brandPartner = $this->brandPartner();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $brandPartner->update($validated);

        return back()->with('success', __('Profile updated successfully.'));
    }

    /**
     * Update the brand partner password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:brand_partner'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $this->brandPartner()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', __('Password updated successfully.'));
    }

    /**
     * Update the brand partner logo.
     */
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'max:2048'], // 2MB max
        ]);

        $brandPartner = $this->brandPartner();

        $extension = $request->file('logo')->getClientOriginalExtension();
        $timestamp = now()->format('Ymd_His');

        $brandPartner->clearMediaCollection('logo');
        $brandPartner->addMedia($request->file('logo'))
            ->usingFileName("{$brandPartner->id}_{$timestamp}.{$extension}")
            ->toMediaCollection('logo');

        return back()->with('success', __('Logo updated successfully.'));
    }
}
