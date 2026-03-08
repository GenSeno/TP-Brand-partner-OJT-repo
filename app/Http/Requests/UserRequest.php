<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')),
            ],
            'password' => [
                Rule::requiredIf(!$this->route('user')),
                'nullable',
                'string',
                'min:8',
                'confirmed'
            ],
            'status' => ['required', Rule::in(UserStatus::cases())],
            'avatar' => 'nullable|image|max:3000',
            'avatar_removed' => 'sometimes|boolean',
            'verified' => 'sometimes|boolean',
        ];
    }
}
