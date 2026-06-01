<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    protected function parseName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? '';
        return [$firstName, $lastName];
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'line1'      => 'required|string|max:255',
            'line2'      => 'nullable|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'province'   => 'nullable|string|max:255',
            'city'       => 'required|string|max:255',
            'barangay'   => 'nullable|string|max:255',
            'postcode'   => 'required|string|max:20',
            'default'    => 'boolean',
        ]);

        $user = $request->user();
        [$firstName, $lastName] = $this->parseName($request->name);

        if ($request->boolean('default')) {
            $user->addresses()->update(['default' => false]);
        }

        $user->addresses()->create([
            'first_name' => $firstName,
            'last_name'  => $lastName ?? $firstName,
            'title'      => '',
            'line1'      => $request->line1,
            'line2'      => $request->line2,
            'country_id' => $request->country_id,
            'province'   => $request->province,
            'city'       => $request->city,
            'barangay'   => $request->barangay,
            'postcode'   => $request->postcode,
            'default'    => $request->boolean('default'),
            'type'       => \App\Enums\AddressType::SHIPPING,
        ]);

        return back()->with('success', 'Address added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'line1'      => 'required|string|max:255',
            'line2'      => 'nullable|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'province'   => 'nullable|string|max:255',
            'city'       => 'required|string|max:255',
            'barangay'   => 'nullable|string|max:255',
            'postcode'   => 'required|string|max:20',
            'default'    => 'boolean',
        ]);

        $user = $request->user();
        $address = $user->addresses()->findOrFail($id);

        if ($request->boolean('default')) {
            $user->addresses()->where('id', '!=', $id)->update(['default' => false]);
        }

        [$firstName, $lastName] = $this->parseName($request->name);

        $address->update([
            'first_name' => $firstName,
            'last_name'  => $lastName ?? $firstName,
            'title'      => '',
            'line1'      => $request->line1,
            'line2'      => $request->line2,
            'country_id' => $request->country_id,
            'province'   => $request->province,
            'city'       => $request->city,
            'barangay'   => $request->barangay,
            'postcode'   => $request->postcode,
            'default'    => $request->boolean('default'),
        ]);

        return back()->with('success', 'Address updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return back()->with('success', 'Address removed successfully.');
    }
}
