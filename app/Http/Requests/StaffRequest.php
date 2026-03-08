<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('staff', 'email')
                    ->ignore($this->route('staff')),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => [
                Rule::requiredIf(!$this->route('staff')),
                'nullable',
                'string',
                'min:8',
                'confirmed'
            ],
            'status' => ['required', Rule::in(UserStatus::cases())],
            'avatar' => 'nullable|image|max:3000',
            'avatar_removed' => 'sometimes|boolean',
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}
