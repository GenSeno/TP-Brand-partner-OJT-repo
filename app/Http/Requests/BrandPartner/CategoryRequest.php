<?php

namespace App\Http\Requests\BrandPartner;

use App\Enums\BrandPartnerCategoryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $brandPartnerId = Auth::guard('brand_partner')->id();
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brand_partner_categories', 'slug')
                    ->where('brand_partner_id', $brandPartnerId)
                    ->ignore($categoryId),
            ],
            'type' => ['required', Rule::enum(BrandPartnerCategoryType::class)],
            'enabled' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
