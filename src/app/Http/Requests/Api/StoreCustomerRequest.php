<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'document' => ['required', 'string', 'max:20', 'unique:customers,document'],
            'last_access_at' => ['nullable', 'date'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.label' => ['nullable', 'string', 'max:100'],
            'addresses.*.street' => ['required_with:addresses', 'string', 'max:255'],
            'addresses.*.number' => ['required_with:addresses', 'string', 'max:20'],
            'addresses.*.complement' => ['nullable', 'string', 'max:255'],
            'addresses.*.neighborhood' => ['required_with:addresses', 'string', 'max:255'],
            'addresses.*.city' => ['required_with:addresses', 'string', 'max:255'],
            'addresses.*.state' => ['required_with:addresses', 'string', 'size:2'],
            'addresses.*.zip_code' => ['required_with:addresses', 'string', 'max:10'],
            'addresses.*.is_primary' => ['nullable', 'boolean'],
        ];
    }
}