<?php

namespace App\Http\Requests;

use App\Constants\StaffPermission;
use App\Models\InventoryItem;
use Illuminate\Foundation\Http\FormRequest;

class JobOrderInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        $staff = $this->user('staff');

        // hasAnyPermission() bypasses Gate::before, so admins would fail.
        // Use can() which respects the Gate::before admin bypass.
        return $staff->can(StaffPermission::MANAGE_JO_PRINTING_TASKS)
            || $staff->can(StaffPermission::MANAGE_JO_HEAT_PRESS_TASKS)
            || $staff->can(StaffPermission::MANAGE_JO_SEWING_TASKS)
            || $staff->can(StaffPermission::MANAGE_JO_PACKING_TASKS);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_order_id' => ['required', 'integer', 'exists:job_orders,id'],
            'items' => ['required', 'array'],
            // nullable: empty dropdown rows are skipped server-side, not rejected
            'items.*.inventory_item_id' => ['nullable', 'integer', 'exists:inventory_items,id'],
            'items.*.amount_used' => ['nullable', 'numeric', 'min:0'],
        ];
    }    /**
         * Get custom attributes for validator errors.
         *
         * @return array
         */
    public function attributes()
    {
        return [
            'items.*.amount_used' => 'qty',
        ];
    }
}
