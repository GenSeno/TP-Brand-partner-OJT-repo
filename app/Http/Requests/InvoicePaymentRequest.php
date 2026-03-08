<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class InvoicePaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'gt:0',
                // function ($attribute, $value, $fail) {
                //     /** @var Invoice $invoice */
                //     $invoice = $this->route('billing');
                //     $summary = $invoice->append('summary')->summary;
                //     $amount = (int) round($value * 100);
                //     if ($amount > $summary['amount_balance']->value) {
                //         $fail('The ' . $attribute . ' may not be greater than the remaining balance.');
                //     }
                // },
               function ($attribute, $value, $fail) {
                    /** @var Invoice $invoice */
                    $invoice = $this->route('billing');
                    $amount = (int) round($value * 100);

                    // Get current payment ID if updating
                    $currentPaymentId = $this->route('payment')?->id;

                    // Sum all existing payments, excluding current if updating
                    $paidExcludingCurrent = $invoice->payments()
                        ->when($currentPaymentId, fn($q) => $q->where('id', '<>', $currentPaymentId))
                        ->sum('amount');

                    // Remaining balance
                    $remainingBalance = $invoice->amount_due->value - $paidExcludingCurrent;

                    if ($amount > $remainingBalance) {
                        $fail('The ' . $attribute . ' may not be greater than the remaining balance (' . number_format($remainingBalance / 100, 2) . ').');
                    }
                },
            ],
            'paid_at' => ['required', 'date'],
            'internal_reference' => ['nullable', 'string'],
            'reference' => ['nullable', 'string'],
            'method' => ['required', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:25600'],
        ];
    }

    public function attributes(): array
    {
        return [
            'paid_at' => 'date paid',
            'internal_reference' => 'reference',
        ];
    }
}
