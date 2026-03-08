<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductOptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'values' => 'required|array|min:1',
            'values.*.label' => 'required|string|max:255',
            'values.*.value' => 'required|string|max:255',
            'autoapply' => 'sometimes|boolean',
            'position' => 'required|integer|min:1',
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
            'values.*.label' => 'label',
            'values.*.value' => 'value',
        ];
    }
}
