<?php

namespace App\DTOs;

class CheckoutData
{
    public function __construct(
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly string $customerPhone,
        public readonly string $deliveryType,
        public readonly ?string $deliveryAddress,
        public readonly ?string $deliveryInstructions,
        public readonly ?string $checkoutType = null,
        public readonly ?string $password = null
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            customerName: $validated['customer_name'],
            customerEmail: $validated['customer_email'],
            customerPhone: $validated['customer_phone'],
            deliveryType: $validated['delivery_type'],
            deliveryAddress: $validated['delivery_address'] ?? null,
            deliveryInstructions: $validated['delivery_instructions'] ?? null,
            checkoutType: $validated['checkout_type'] ?? null,
            password: $validated['password'] ?? null
        );
    }
} 