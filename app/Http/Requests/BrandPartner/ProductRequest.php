<?php

namespace App\Http\Requests\BrandPartner;

use App\Enums\BrandPartnerProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brand_partner_products', 'slug')
                    ->where('brand_partner_id', $brandPartnerId)
                    ->ignore($productId),
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('brand_partner_categories', 'id')
                    ->where('brand_partner_id', $brandPartnerId),
            ],
            'event_id' => [
                'nullable',
                'integer',
                Rule::exists('brand_partner_events', 'id')
                    ->where('brand_partner_id', $brandPartnerId),
            ],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100'],
            'colors' => ['nullable', 'string', 'max:1000'],
            'sizes' => ['nullable', 'string', 'max:1000'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            'status' => ['required', Rule::enum(BrandPartnerProductStatus::class)],
            'featured' => ['nullable', 'boolean'],
        ];
    }
}
