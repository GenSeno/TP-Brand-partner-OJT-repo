<?php

namespace App\Http\Requests;

use App\Models\JobOrder;
use App\Models\JobOrderProduction;
use App\Models\OrderLine;
use App\States\JobOrderState\Approval;
use App\States\JobOrderState\Artist;
use App\States\JobOrderState\Completed;
use App\States\JobOrderState\JobOrderState;
use App\States\JobOrderState\NewOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobOrderProductionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_line_id' => ['required', 'integer', 'exists:order_lines,id'],
            'staff_id' => ['sometimes', 'integer', 'exists:staff,id'],
            'state' => [
                'required',
                'string',
                Rule::in(
                    JobOrderState::all()->keys()->diff([
                        NewOrder::$name,
                        Artist::$name,
                        Approval::$name,
                        Completed::$name,
                    ])
                )
            ],
            'quantity' => [
                'required',
                'integer',
                'gt:0',
                function ($attribute, $value, $fail) {
                    $jobOrderId = $this->route('jobOrder')->id;
                    $orderLineId = $this->input('order_line_id');
                    $state = $this->input('state');

                    $jobOrder = JobOrder::find($jobOrderId);
                    if (!$jobOrder) {
                        return;
                    }

                    $orderLine = OrderLine::find($orderLineId);
                    if (!$orderLine) {
                        return;
                    }

                    $received = $this->getReceivedQuantity($jobOrder, $orderLine, $state);

                    $totalProduced = JobOrderProduction::where([
                        'order_line_id' => $orderLineId,
                        'job_order_id' => $jobOrderId,
                        'state' => $state,
                    ])->sum('quantity');

                    $remainingQuantity = $received - $totalProduced;

                    if ($value > $remainingQuantity) {
                        $fail("Quantity exceeds remaining amount to be produced.");
                    }
                },
            ],
            'meta' => ['required', 'array'],
            'meta.sewer' => [
                // Rule::requiredIf($this->input('state') === Sewing::$name),
                'nullable',
                'string',
                'max:255',
            ],
            'meta.names' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $orderLineId = $this->input('order_line_id');
                    $orderLine = OrderLine::find($orderLineId);
                    if (!$orderLine) {
                        return;
                    }

                    if (empty($orderLine->meta['names'])) {
                        return;
                    } elseif (empty($value)) {
                        $fail("Names are required for this order.");
                    }

                    if (count($value) !== $this->input('quantity')) {
                        $fail("Names provided must match the quantity produced.");
                    }
                },
            ]
        ];
    }

    public function attributes()
    {
        return [
            'meta' => 'names',
            'meta.names' => 'names',
        ];
    }

    protected function getReceivedQuantity(JobOrder $jobOrder, OrderLine $orderLine, string $state): int
    {
        $startedStages = $jobOrder->stages()
            ->whereNotNull('started_at')
            ->whereNotIn('state', [NewOrder::$name, Artist::$name, Approval::$name, Completed::$name])
            ->orderBy('started_at')
            ->get()
            ->pluck('state')
            ->map(fn($s) => $s->getMorphClass());

        $currentIndex = $startedStages->search($state);

        // First production stage or state not found — use order line quantity
        if ($currentIndex === false || $currentIndex === 0) {
            return $orderLine->quantity;
        }

        // Subsequent stages — use previous stage's total produced
        $previousState = $startedStages[$currentIndex - 1];

        return (int) JobOrderProduction::where([
            'order_line_id' => $orderLine->id,
            'job_order_id' => $jobOrder->id,
            'state' => $previousState,
        ])->sum('quantity');
    }
}
