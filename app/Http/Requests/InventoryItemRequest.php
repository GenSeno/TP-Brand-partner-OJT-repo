<?php

namespace App\Http\Requests;

use App\Enums\InventoryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(InventoryType::class)],
            'item_name' => 'required|string|max:255',
            'current_stock' => 'required|numeric|min:0',
            'uom_code' => 'required|string|exists:unit_measures,code',
            'notes' => 'nullable|string|max:1000',
            'reorder_qty' => 'nullable|numeric|min:0',
            'enabled' => 'required|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'item_name' => 'item name',
            'current_stock' => 'current stock',
            'uom_code' => 'unit of measure',
            'reorder_qty' => 're-order qty',
            'enabled' => 'status',
        ];
    }
}
