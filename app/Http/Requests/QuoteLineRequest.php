<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteLineRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'custom_dimension' => 'nullable|string',
            'sizes' => 'required|array|min:1',
            'sizes.*.variant_id' => 'required|exists:product_variants,id',
            'sizes.*.product_id' => 'required|exists:products,id',

            'sizes.*.size_label' => 'required|string',

            'sizes.*.quantity' => 'required|numeric|min:1',
            'sizes.*.price' => 'required|numeric|min:0',

            'sizes.*.names' => 'nullable|array',
            'sizes.*.names.*' => 'nullable|string'
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'selectedCategoryId.required' => 'Please select category.',
    //         'selectedProduct.required' => 'Please select product.',
    //         'quote_lines.required' => 'Please select at least one item.',
    //         'quote_lines.array' => 'The items format is invalid.',

    //         'quote_lines.*.purchasable_type.required' =>
    //             'The item type is required.',

    //         'quote_lines.*.purchasable_id.required' =>
    //             'The selected item is invalid.',

    //         'quote_lines.*.quantity.required' =>
    //             'Quantity is required for each item.',
    //         'quote_lines.*.quantity.min' =>
    //             'Quantity must be at least 1.',

    //         'quote_lines.*.purchase_price.required' =>
    //             'Price is required for each item.',
    //         'quote_lines.*.purchase_price.min' =>
    //             'Price must not be negative.',

    //         // 'quote_lines.*.meta.uom.max' =>
    //         //     'UOM must not exceed 50 characters.',
    //         // 'quote_lines.*.meta.uom.required' =>
    //         //     'Please select UOM.',
    //         // 'quote_lines.*.meta.description.max' =>
    //         //     'Item description must not exceed 255 characters.',

    //         'quote_lines.*.meta.names.array' =>
    //             'Names must be a valid list.',
    //         'quote_lines.*.meta.names.*.max' =>
    //             'Each name must not exceed 255 characters.',
    //     ];
    // }

    // protected function prepareForValidation()
    // {
    //     $lines = $this->input('quote_lines', []);

    //     $this->merge([
    //         'quote_lines' => collect($lines)->map(function ($line) {
    //             return array_merge($line, [
    //                 'quantity' => isset($line['quantity'])
    //                     ? (int) $line['quantity']
    //                     : null,

    //                 'purchase_price' => isset($line['purchase_price'])
    //                     ? (float) $line['purchase_price']
    //                     : null,
    //             ]);
    //         })->toArray(),
    //     ]);
    // }
}
