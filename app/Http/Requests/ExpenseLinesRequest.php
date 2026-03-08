<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseLinesRequest extends FormRequest
{
    public function authorize()
    {
        return true; // you can add permission logic here
    }

    public function rules()
    {
        return [
            'expense_lines' => 'required|array|min:1',
            'expense_lines.*.id' => 'nullable|exists:expense_lines,id',
            'expense_lines.*.expense_id' => 'required|exists:expenses,id',
            'expense_lines.*.expense_account_id' => 'required|exists:expense_accounts,id',
            'expense_lines.*.description' => 'max:255',
            'expense_lines.*.qty' => 'required|numeric|min:1',
            'expense_lines.*.price' => 'numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'expense_lines.required' => 'Please add at least one expense line.',
            'expense_lines.*.expense_account_id.required' => 'Select an expense account.',
            'expense_lines.*.expense_account_id.exists' => 'Select an expense account.',
            'expense_lines.*.qty.required' => 'Qty is required.',
            'expense_lines.*.price.min' => 'Price must be at least 0.',
            'expense_lines.*.qty.min' => 'Qty must be at least 1.',
            'expense_lines.*.price.min' => 'Price must be at least 0.',
            'expense_lines.*.price.numeric' => 'Price must be a number.',
        ];
    }
}
