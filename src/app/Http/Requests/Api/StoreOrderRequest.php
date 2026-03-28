<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\OrderStatusEnum;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'address_id' => ['required', 'exists:addresses,id'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'source' => ['required', 'string', 'max:50'],
            'status' => ['nullable', new Enum(OrderStatusEnum::class)],
        ];
    }
}