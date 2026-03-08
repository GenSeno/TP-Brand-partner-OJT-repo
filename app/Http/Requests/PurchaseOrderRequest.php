<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'order_date' => ['nullable', 'date'],
            'expected_delivery_date' => ['nullable', 'date'],
            // currency_code is assigned programmatically on the server
            'notes' => ['nullable', 'string'],
            'status' => ['nullable'],
            'lines' => ['array', 'min:1'],
            'lines.*.id' => ['nullable', 'integer', 'exists:purchase_order_lines,id'],
            'lines.*.inventory_item_id' => ['required_with:lines', 'integer','exists:inventory_items,id'],
            'lines.*.unit_price' => ['required_with:lines'],
            'lines.*.unit_quantity' => ['nullable', 'numeric'],
            'lines.*.description' => ['nullable'],
            'lines.*.quantity' => ['required_with:lines', 'numeric'],
            'deleted_line_ids' => ['sometimes', 'array'],
            'deleted_line_ids.*' => ['integer', 'exists:purchase_order_lines,id'],
        ];
    }
}
