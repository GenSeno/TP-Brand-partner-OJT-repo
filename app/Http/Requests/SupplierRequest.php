<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'country_id' => 'nullable|exists:countries,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:50',
            'enabled' => 'required|boolean',
            'notes' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|max:3000',
            'logo_removed' => 'sometimes|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'country_id' => 'country',
            'contact_person' => 'contact person',
            'phone' => 'phone',
        ];
    }
}
