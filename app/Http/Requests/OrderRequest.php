<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable'],

            'address' => ['required', 'array'],
            'address.first_name' => ['required', 'string', 'max:255'],
            'address.last_name' => ['required', 'string', 'max:255'],
            'address.title' => ['nullable', 'string', 'max:50'],
            'address.company_name' => ['nullable', 'string', 'max:255'],
            'address.email' => ['required', 'email', 'max:255'],
            'address.phone' => ['required', 'string', 'max:50'],
            'address.line1' => ['required', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.province' => ['required', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.barangay' => ['nullable', 'string', 'max:255'],
            'address.postcode' => ['required', 'string', 'max:20'],
            'address.country_id' => ['required', 'exists:countries,id'],
            'address.notes' => ['nullable', 'string', 'max:5000'],

            'shipping' => ['nullable', 'array'],
            'shipping.first_name' => ['required_with:shipping', 'string', 'max:255'],
            'shipping.last_name' => ['required_with:shipping', 'string', 'max:255'],
            'shipping.title' => ['nullable', 'string', 'max:50'],
            'shipping.line1' => ['required_with:shipping', 'string', 'max:255'],
            'shipping.line2' => ['nullable', 'string', 'max:255'],
            'shipping.province' => ['required_with:shipping', 'string', 'max:255'],
            'shipping.city' => ['required_with:shipping', 'string', 'max:255'],
            'shipping.barangay' => ['nullable', 'string', 'max:255'],
            'shipping.postcode' => ['required_with:shipping', 'string', 'max:20'],
            'shipping.country_id' => ['required_with:shipping', 'exists:countries,id'],
            'need' => ['nullable', 'date'],
            'shipping_option' => ['nullable', 'string', 'in:pickup,delivery'],
        ];
    }

    public function attributes()
    {
        return [
            'customer_id' => 'customer',
            'order_date' => 'order date',
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
            'address.notes' => 'notes',
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
            'need' => 'when do you need it',
        ];
    }
}
