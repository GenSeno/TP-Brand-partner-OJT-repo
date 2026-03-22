<?php

namespace App\Http\Requests\BrandPartner;

use Illuminate\Foundation\Http\FormRequest;

class ProductOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'position'         => ['required', 'integer', 'min:1'],
            'values'           => ['required', 'array', 'min:1'],
            'values.*.label'   => ['required', 'string', 'max:255'],
            'values.*.value'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'values.*.label' => 'label',
            'values.*.value' => 'value',
        ];
    }
}
