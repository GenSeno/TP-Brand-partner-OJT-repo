<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Get the customer ID for update (if exists)
        $customerId = $this->route('customer') ? $this->route('customer')->id : null;

        return [
            'title' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'enabled' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Email with unique validation
            'address.email' => [
                'required',
                'email',
                Rule::unique('addresses', 'email')
                    ->where(function ($query) use ($customerId) {
                        $query->where('addressable_type', \App\Models\Customer::class)
                            ->where('addressable_id', '<>', $customerId);
                    }),
            ],
            
            // Address rules
            'address.title' => 'nullable|string|max:10',
            'address.first_name' => 'required|string|max:255',
            'address.last_name' => 'required|string|max:255',
            'address.company_name' => 'nullable|string|max:255',
            'address.line1' => 'required|string|max:255',
            'address.line2' => 'nullable|string|max:255',
            'address.city' => 'required|string|max:255',
            'address.province' => 'required|string|max:255',
            'address.barangay' => 'nullable|string|max:255',
            'address.postcode' => 'required|string|max:20',
            'address.country_id' => 'required|exists:countries,id',
            'address.phone' => 'required|numeric|digits_between:7,15',

  

            'shipping' => 'nullable|array',

            // Shipping Name
            'shipping.title'        => 'nullable|string|max:10',
            'shipping.first_name'   => 'nullable|string|max:255',
            'shipping.last_name'    => 'nullable|string|max:255',
            'shipping.company_name' => 'nullable|string|max:255',

            'shipping.email'       => 'nullable|email',
            'shipping.phone'       => 'nullable|string|max:20',

            'shipping.line1'       => 'required_with:shipping|string|max:255',
            'shipping.line2'       => 'nullable|string|max:255',
            'shipping.city'        => 'required_with:shipping|string|max:255',
            'shipping.province'    => 'required_with:shipping|string|max:255',
            'shipping.barangay'    => 'nullable|string|max:255',
            'shipping.postcode'    => 'required_with:shipping|string|max:20',
            'shipping.country_id'  => 'required_with:shipping|exists:countries,id',


        ];
    }

    public function messages()
    {
        return [
            // Customer field messages
            'title.max' => 'Title must not exceed 10 characters.',
            'first_name.required' => 'The First Name field is required.',
            'first_name.string' => 'The First Name must be a string.',
            'first_name.max' => 'The First Name must not exceed 255 characters.',
            'last_name.required' => 'The Last Name field is required.',
            'last_name.string' => 'The Last Name must be a string.',
            'last_name.max' => 'The Last Name must not exceed 255 characters.',
            'company_name.string' => 'The Company Name must be a string.',
            'company_name.max' => 'The Company Name must not exceed 255 characters.',
            'enabled.boolean' => 'The Enabled field must be true or false.',
            
            // Avatar messages
            'avatar.image' => 'The Avatar must be a valid image file.',
            'avatar.mimes' => 'The Avatar must be a JPEG, PNG, or JPG file.',
            'avatar.max' => 'The Avatar must not exceed 2MB in size.',
            
            // Address field messages
            'address.email.required' => 'The Email field is required.',
            'address.email.email' => 'The Email must be a valid email address.',
            'address.email.unique' => 'This Email address is already registered.',
            'address.phone.required' => 'The Phone field is required.',
            'address.phone.numeric' => 'The Phone must be a number.',
            'address.phone.digits_between' => 'The Phone must be between 7 and 15 digits.',
            'address.line1.required' => 'The Street Address field is required.',
            'address.line1.string' => 'The Street Address must be a string.',
            'address.line1.max' => 'The Street Address must not exceed 255 characters.',
            'address.line2.string' => 'The Address Line 2 must be a string.',
            'address.line2.max' => 'The Address Line 2 must not exceed 255 characters.',
            'address.city.required' => 'The City field is required.',
            'address.city.string' => 'The City must be a string.',
            'address.city.max' => 'The City must not exceed 255 characters.',
            'address.province.required' => 'The Province field is required.',
            'address.province.string' => 'The Province must be a string.',
            'address.province.max' => 'The Province must not exceed 255 characters.',
            'address.barangay.string' => 'The Barangay must be a string.',
            'address.barangay.max' => 'The Barangay must not exceed 255 characters.',
            'address.postcode.required' => 'The Postal Code field is required.',
            'address.postcode.string' => 'The Postal Code must be a string.',
            'address.postcode.max' => 'The Postal Code must not exceed 20 characters.',
            'address.country_id.required' => 'The Country field is required.',
            'address.country_id.exists' => 'The selected Country is invalid.',
            'address.email.required' => 'The Address Email field is required.',
            'address.email.email' => 'The Address Email must be a valid email address.',
            'address.phone.required' => 'The Address Phone field is required.',
            'address.phone.string' => 'The Address Phone must be a string.',
            'address.phone.max' => 'The Address Phone must not exceed 20 characters.',
          

            'shipping.line1.required_with' => 'The Shipping Street Address is required.',
            'shipping.city.required_with'  => 'The Shipping City is required.',
            'shipping.country_id.required_with' => 'The Shipping Country is required.',
            'shipping.postcode.required_with' => 'The Shipping Postal Code is required.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'enabled' => $this->boolean('enabled'),
        ]);
    }
}