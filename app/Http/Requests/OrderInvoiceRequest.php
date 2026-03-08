<?php

namespace App\Http\Requests;

use App\Enums\InvoiceType;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderInvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoiced_at' => ['required', 'date'],
            'type' => [
                'required',
                'string',
                Rule::enum(InvoiceType::class),
            ],
            'amount_due' => [
                Rule::requiredIf(function () {
                    return $this->input('type') !== InvoiceType::FINAL_PAYMENT->value;
                }),
                'nullable',
                'numeric',
                'gt:0',
                function ($attribute, $value, $fail) {
                    /** @var Order $order */
                    $order = $this->route('order')
                        ->append('billing_summary');
                    if ($order->billing_summary['amount_unbilled']->decimal < $value) {
                        $fail('The amount should not be greater than the amount unbilled.');
                    }
                },
            ],
            'due_at' => ['required', 'date', 'after_or_equal:invoiced_at'],
        ];
    }

    public function attributes(): array
    {
        return [
            'invoiced_at' => 'date',
            'due_at' => 'due date',
            'type' => 'billing type',
            'amount_due' => 'amount',
        ];
    }
}
