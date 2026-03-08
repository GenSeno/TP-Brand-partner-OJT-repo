<?php

namespace App\Http\Requests;

use App\Constants\StaffPermission;
use App\Enums\FabricType;
use App\Enums\JobOrderUrgency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'urgency_flag' => ['sometimes', Rule::in(JobOrderUrgency::cases())],

            'deadlines' => [
                Rule::requiredIf(
                    $this->user('staff')
                        ->can(StaffPermission::MANAGE_JO_DEADLINES)
                ),
                'array'
            ],
            'deadlines.*.state' => ['required', 'string'],
            'deadlines.*.due_at' => ['required', 'date'],

            'products' => [
                Rule::requiredIf(
                    $this->user('staff')
                        ->can([
                            StaffPermission::MANAGE_JO_NEW_ORDER_TASKS,
                            StaffPermission::MANAGE_JO_ARTIST_TASKS
                        ])
                ),
                'array'
            ],
            'products.*.id' => ['required', 'integer', 'exists:job_order_products,id'],
            'products.*.inventory_item_id' => [
                Rule::requiredIf(
                    $this->user('staff')
                        ->can(StaffPermission::MANAGE_JO_NEW_ORDER_TASKS)
                ),
                'nullable',
                'integer',
                'exists:inventory_items,id'
            ],
            'products.*.file_path' => [
                Rule::requiredIf(
                    !$this->user('staff')
                        ->can(StaffPermission::MANAGE_JO_NEW_ORDER_TASKS) &&
                    $this->user('staff')
                        ->can(StaffPermission::MANAGE_JO_ARTIST_TASKS)
                ),
                'nullable',
                'string',
                'max:255'
            ],
            'products.*.notes' => ['nullable', 'string', 'max:1000'],

            'dispatching' => ['sometimes', 'array'],
            'dispatching.dispatching_type' => [
                Rule::requiredIf($this->isDispatchingStaff()),
                'nullable',
                Rule::in(['delivery', 'pickup']),
            ],
            'dispatching.delivery_method' => [
                Rule::requiredIf($this->isDispatchingStaff()),
                'nullable',
                'string',
                'max:255',
            ],
            'dispatching.reference_number' => [
                Rule::requiredIf($this->isDispatchingStaff()),
                'nullable',
                'string',
                'max:255',
            ],
            'dispatching.add_delivery_fee' => [
                Rule::requiredIf($this->isDispatchingStaff()),
                'nullable',
                Rule::in(['yes', 'no']),
            ],
        ];
    }

    private function isDispatchingStaff(): bool
    {
        return !$this->user('staff')->admin && $this->has('dispatching') &&
            $this->user('staff')->can(StaffPermission::MANAGE_JO_DISPATCHING_TASKS);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'deadlines.*.state' => 'stage',
            'deadlines.*.due_at' => 'due date',

            'products.*.id' => 'product',
            'products.*.fabric_type' => 'fabric',
            'products.*.notes' => 'notes',
        ];
    }
}
