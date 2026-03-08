<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Core
            'expense_date' => ['required', 'date'],
            'supplier_id'  => ['required', 'exists:suppliers,id'],
            'description'  => ['nullable', 'string'],

            // Payment
            'payment_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', Rule::in([
                'cash',
                'bank',
                'check',
                'gcash',
            ])],
            'reference_no' => ['nullable', 'string', 'max:255'],

            // Status
            'status' => ['required', Rule::in([
                'draft',
                'upcoming',
                'paid',
                'cancelled',
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'expense_date.required' => 'Expense date is required.',
            'supplier_id.required'  => 'Please select a supplier.',
            'supplier_id.exists'    => 'Selected supplier is invalid.',
            'status.required'       => 'Status is required.',
        ];
    }

    /**
     * Prepare data before validation
     */
    protected function prepareForValidation(): void
    {
        // Normalize empty strings to null
        $this->merge([
            'description' => $this->description ?: null,
            'payment_method' => $this->payment_method ?: null,
            'payment_reference_no' => $this->payment_reference_no ?: null,
            'payment_date' => $this->payment_date ?: null,
        ]);
    }
}
