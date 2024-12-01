<?php

namespace App\DTOs;

class CartData
{
    public function __construct(
        public readonly int $menuItemId,
        public readonly int $quantity,
        public readonly ?string $specialInstructions = null
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            menuItemId: $validated['menu_item_id'],
            quantity: $validated['quantity'],
            specialInstructions: $validated['special_instructions'] ?? null
        );
    }
} 