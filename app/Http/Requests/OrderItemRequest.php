<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            // 'quantity' => ['required', 'integer', 'min:1'],
            // 'price' => ['required', 'numeric', 'min:0'],
            // 'values' => ['sometimes', 'array'],
            // 'values.*' => ['required', 'exists:product_option_values,value'],
            'sizes' => 'required|array|min:1',
            'sizes.*.variant_id' => 'required|exists:product_variants,id',
            'sizes.*.product_id' => 'required|exists:products,id',
            'sizes.*.names.*' => [
                'sometimes',
                'array',
            ],
            'sizes.*.size_label' => 'required|string',
            'sizes.*.quantity' => 'required|numeric|min:1',
            'sizes.*.price' => 'required|numeric|min:0',
            'sizes.*.names.*' => [
                Rule::requiredIf(function () {
                    $values = $this->input('values', []);
                    return in_array('WITH-name', $values);
                }),
                'nullable',
                'string',
            ],
            'custom_dimension' => [
                Rule::requiredIf(function () {
                    $values = $this->input('values', []);
                    return in_array('custom', $values);
                }),
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'product_id' => 'product',
            'values' => 'variant',
            'names.*' => 'names',
        ];
    }
}
