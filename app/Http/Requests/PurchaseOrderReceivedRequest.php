<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderReceivedRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'notes' => ['nullable', 'string'],
            'date_received' => ['required', 'date', 'after_or_equal:order_date'],
            'order_date' => ['required', 'date'],
            'status' => ['nullable'],
            'lines' => ['array', 'min:1'],
            'lines.*.id' => ['nullable', 'integer', 'exists:purchase_order_lines,id'],
            'lines.*.unit_price' => ['required_with:lines'],
            'lines.*.qty_received' => ['required', 'numeric'],
        ];
    }
}
