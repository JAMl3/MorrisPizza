<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address' => 'required_if:delivery_type,delivery|nullable|string|max:255',
            'delivery_instructions' => 'nullable|string|max:500',
            'checkout_type' => 'nullable|in:guest,create_account',
            'password' => 'required_if:checkout_type,create_account|nullable|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'delivery_address.required_if' => 'The delivery address is required for delivery orders.',
            'password.required_if' => 'A password is required when creating an account.',
        ];
    }
} 