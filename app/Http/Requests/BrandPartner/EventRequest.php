<?php

namespace App\Http\Requests\BrandPartner;

use App\Enums\BrandPartnerEventStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $brandPartnerId = Auth::guard('brand_partner')->id();
        $eventId = $this->route('event')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brand_partner_events', 'slug')
                    ->where('brand_partner_id', $brandPartnerId)
                    ->ignore($eventId),
            ],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::enum(BrandPartnerEventStatus::class)],
            'enabled' => ['nullable', 'boolean'],
        ];
    }
}
