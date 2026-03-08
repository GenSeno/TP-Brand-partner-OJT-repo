<?php

namespace App\Http\Requests;

use App\Enums\InventoryMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryAdjustmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(InventoryMovementType::class)],
            'amount' => 'required|numeric|gt:0',
            'notes' => 'required|string|max:1000',
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => 'adjustment type',
        ];
    }
}
