<?php

namespace App\Http\Requests;

use App\Enums\BrandPartnerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class BrandPartnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $brandPartnerId = $this->route('brand_partner')?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brand_partners', 'slug')->ignore($brandPartnerId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('brand_partners', 'email')->ignore($brandPartnerId),
            ],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(BrandPartnerStatus::class)],
            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_removed' => ['nullable', 'boolean'],
        ];

        // Password is required on create, optional on update
        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', Password::defaults()];
        } else {
            $rules['password'] = ['nullable', Password::defaults()];
        }

        return $rules;
    }
}
