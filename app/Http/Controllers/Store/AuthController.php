<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Merge any guest session cart into the user's DB cart
            $cartController = new BrandPartnerCartController;
            $cartController->mergeSessionCartIntoDb($request, Auth::id());

            return redirect()->intended(route('store.brand-partner.index', config('store.brand_partner_slug')));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        // Merge any guest session cart into the new user's DB cart
        $cartController = new BrandPartnerCartController;
        $cartController->mergeSessionCartIntoDb($request, $user->id);

        return redirect(route('store.brand-partner.index', config('store.brand_partner_slug')));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(route('store.brand-partner.index', config('store.brand_partner_slug')));
    }

    public function account(Request $request)
    {
        $brandPartner = \App\Models\BrandPartner::where('slug', config('store.brand_partner_slug'))
            ->where('status', \App\Enums\BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $orders = \App\Models\BrandPartnerOrder::where('brand_partner_id', $brandPartner->id)
            ->where('user_id', $request->user()->id)
            ->with(['lines.product.images'])
            ->orderBy('created_at', 'desc')
            ->get();

        $user = $request->user()->load('addresses.country');
        $countries = \App\Models\Country::orderBy('name')->get();
        $defaultCountryId = \App\Models\Country::where('iso2', 'PH')->value('id');

        return Inertia::render('store/storeuser', [
            'user' => $user,
            'brandPartner' => $brandPartner,
            'orders' => $orders,
            'countries' => $countries,
            'defaultCountryId' => $defaultCountryId,
        ]);
    }

    public function cancelOrder(Request $request, string $reference)
    {
        $brandPartner = \App\Models\BrandPartner::where('slug', config('store.brand_partner_slug'))
            ->where('status', \App\Enums\BrandPartnerStatus::ACTIVE)
            ->firstOrFail();

        $order = \App\Models\BrandPartnerOrder::where('brand_partner_id', $brandPartner->id)
            ->where('user_id', $request->user()->id)
            ->where('reference', $reference)
            ->firstOrFail();

        if (in_array($order->status->value, ['pending', 'confirmed'])) {
            $order->cancel();
        }

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'dobDay' => 'nullable|string|max:2',
            'dobMonth' => 'nullable|string|max:2',
            'dobYear' => 'nullable|string|max:4',
        ]);

        $name = trim($validated['firstName'].' '.$validated['lastName']);

        $dateOfBirth = null;
        if (! empty($validated['dobDay']) && ! empty($validated['dobMonth']) && ! empty($validated['dobYear'])) {
            $dateOfBirth = $validated['dobYear'].'-'.$validated['dobMonth'].'-'.$validated['dobDay'];
        }

        $request->user()->update([
            'name' => $name,
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $dateOfBirth,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email,'.$request->user()->id,
        ]);

        $request->user()->update([
            'email' => $validated['email'],
        ]);

        return back()->with('success', 'Email updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|different:oldPassword',
            'confirmPassword' => 'required|string|same:newPassword',
        ]);

        $user = $request->user();

        if (! \Hash::check($validated['oldPassword'], $user->password)) {
            throw ValidationException::withMessages([
                'oldPassword' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->update([
            'password' => $validated['newPassword'],
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
