<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class JobOrderSewerAssignmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'assignments' => ['required', 'array', 'min:1'],
            'assignments.*.sewer_id' => ['required', 'integer', 'exists:staff,id'],
            'assignments.*.quantity' => [
                'required',
                'integer',
                'gt:0',
            ],
        ];
    }

    public function withValidator(Validator $validator)
    {
        if ($validator->fails())
            return;

        $validator->after(function (Validator $validator) {
            /** @var \App\Models\OrderLine $orderLine */
            $orderLine = $this->route('orderLine');
            $totalQuantity = $orderLine->quantity;
            $assignedQuantity = collect($this->input('assignments'))->sum('quantity');
            if ($assignedQuantity > $totalQuantity) {
                $validator->errors()->add(
                    key: 'assignments',
                    message: 'Assigned quantity exceeds the required quantity.'
                );
            }
        });
    }

    public function attributes()
    {
        return [
            'assignments' => 'assignments',
            'assignments.*.sewer_id' => 'sewer',
            'assignments.*.quantity' => 'quantity',
        ];
    }
}
