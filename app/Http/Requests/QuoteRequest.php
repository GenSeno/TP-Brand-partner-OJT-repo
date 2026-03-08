<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
{
    public function rules()
    {
        // Get the customer ID for update (if exists)
        $customerId = $this->route('quote') ? $this->route('quote')->id : null;

        return [
            'address' => 'required|array',
            'address.title' => 'nullable|string|max:10',
            'address.first_name' => 'required|string|max:255',
            'address.last_name' => 'required|string|max:255',
            'address.company_name' => 'nullable|string|max:255',
            'address.email' => 'required|string|max:255',
            'address.line1' => 'required|string|max:255',
            'address.line2' => 'nullable|string|max:255',
            'address.city' => 'required|string|max:255',
            'address.province' => 'required|string|max:255',
            'address.barangay' => 'nullable|string|max:255',
            'address.postcode' => 'required|string|max:20',
            'address.country_id' => 'required|exists:countries,id',
            'address.phone' => 'required|string|max:50',
            'address.need' => 'required|string',

            'shipping' => 'nullable|array',
            'shipping.title' => 'nullable|string|max:10',
            'shipping.first_name' => 'nullable|string|max:255',
            'shipping.last_name' => 'nullable|string|max:255',
            'shipping.company_name' => 'nullable|string|max:255',
            'shipping.email' => 'nullable|email',
            'shipping.phone' => 'nullable|string|max:20',
            'shipping.line1' => 'required_with:shipping|string|max:255',
            'shipping.line2' => 'nullable|string|max:255',
            'shipping.city' => 'required_with:shipping|string|max:255',
            'shipping.province' => 'required_with:shipping|string|max:255',
            'shipping.barangay' => 'nullable|string|max:255',
            'shipping.postcode' => 'required_with:shipping|string|max:20',
            'shipping.country_id' => 'required_with:shipping|exists:countries,id',
        ];
    }

    public function attributes()
    {
        return [
            'address.first_name' => 'first name',
            'address.last_name' => 'last name',
            'address.title' => 'title',
            'address.company_name' => 'company name',
            'address.email' => 'email',
            'address.phone' => 'phone',
            'address.line1' => 'street address',
            'address.line2' => 'address line 2',
            'address.province' => 'province',
            'address.city' => 'city',
            'address.barangay' => 'barangay',
            'address.postcode' => 'postal code',
            'address.country_id' => 'country',
            'address.need' => 'when do you need it',
            'shipping.first_name' => 'shipping first name',
            'shipping.last_name' => 'shipping last name',
            'shipping.title' => 'shipping title',
            'shipping.line1' => 'shipping street address',
            'shipping.line2' => 'shipping address line 2',
            'shipping.province' => 'shipping province',
            'shipping.city' => 'shipping city',
            'shipping.barangay' => 'shipping barangay',
            'shipping.postcode' => 'shipping postal code',
            'shipping.country_id' => 'shipping country',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'address.title.max' => 'Title must not exceed 10 characters.',
    //         'address.first_name.required' => 'The First Name field is required.',
    //         'address.first_name.string' => 'The First Name must be a string.',
    //         'address.first_name.max' => 'The First Name must not exceed 255 characters.',
    //         'address.last_name.required' => 'The Last Name field is required.',
    //         'address.last_name.string' => 'The Last Name must be a string.',
    //         'address.last_name.max' => 'The Last Name must not exceed 255 characters.',
    //         'address.company_name.string' => 'The Company Name must be a string.',
    //         'address.company_name.max' => 'The Company Name must not exceed 255 characters.',

    //         'address.email.required' => 'The Email field is required.',
    //         'address.email.email' => 'The Email must be a valid email address.',
    //         'address.email.unique' => 'This Email address is already registered.',
    //         'address.phone.required' => 'The Phone field is required.',
    //         'address.phone.numeric' => 'The Phone must be a number.',
    //         'address.phone.digits_between' => 'The Phone must be between 7 and 15 digits.',
    //         'address.line1.required' => 'The Street Address field is required.',
    //         'address.line1.string' => 'The Street Address must be a string.',
    //         'address.line1.max' => 'The Street Address must not exceed 255 characters.',
    //         'address.line2.string' => 'The Address Line 2 must be a string.',
    //         'address.line2.max' => 'The Address Line 2 must not exceed 255 characters.',
    //         'address.city.required' => 'The City field is required.',
    //         'address.city.string' => 'The City must be a string.',
    //         'address.city.max' => 'The City must not exceed 255 characters.',
    //         'address.province.required' => 'The Province field is required.',
    //         'address.province.string' => 'The Province must be a string.',
    //         'address.province.max' => 'The Province must not exceed 255 characters.',
    //         'address.barangay.string' => 'The Barangay must be a string.',
    //         'address.barangay.max' => 'The Barangay must not exceed 255 characters.',
    //         'address.postcode.required' => 'The Postal Code field is required.',
    //         'address.postcode.string' => 'The Postal Code must be a string.',
    //         'address.postcode.max' => 'The Postal Code must not exceed 20 characters.',
    //         'address.country_id.required' => 'The Country field is required.',
    //         'address.country_id.exists' => 'The selected Country is invalid.',
    //         'address.phone.string' => 'The Address Phone must be a string.',
    //         'address.phone.max' => 'The Address Phone must not exceed 20 characters.',
    //         'address.need.required' => 'The When do you need it field is required.',
    //         'address.need.string' => 'The When do you need it must be a string.',

    //         'shipping.line1.required_with' => 'The Shipping Street Address is required.',
    //         'shipping.city.required_with' => 'The Shipping City is required.',
    //         'shipping.country_id.required_with' => 'The Shipping Country is required.',
    //         'shipping.postcode.required_with' => 'The Shipping Postal Code is required.',
    //     ];
    // }

    protected function prepareForValidation()
    {
        $this->merge([
            'enabled' => $this->boolean('enabled'),
        ]);

        // If shipping is an empty array or has only empty values, set it to null
        if ($this->has('shipping') && is_array($this->shipping)) {
            $hasData = collect($this->shipping)->filter(function ($value) {
                return ! is_null($value) && $value !== '';
            })->isNotEmpty();

            if (! $hasData) {
                $this->merge(['shipping' => null]);
            }
        }
    }
}
