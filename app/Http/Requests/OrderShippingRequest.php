<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderShippingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lines' => 'required|array|min:1',
            'lines.*.amount' => 'required|numeric',
            'lines.*.description' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        $attributes = [];
        foreach ($this->input('lines', []) as $index => $line) {
            $attributes["lines.{$index}.amount"] = 'amount (line ' . ($index + 1) . ')';
            $attributes["lines.{$index}.description"] = 'description (line ' . ($index + 1) . ')';
        }
        return $attributes;
    }
}
