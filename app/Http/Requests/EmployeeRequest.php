<?php
namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\EmployeeJobTitle;

class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', new Enum(EmployeeJobTitle::class)],
            'status' => ['required', new Enum(UserStatus::class)],
            'avatar' => 'nullable|image|max:3000',
            'avatar_removed' => 'sometimes|boolean',
        ];
    }
}
