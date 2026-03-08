<?php

namespace App\Lunar\ValueObjects\Cart;

use App\Lunar\DataTypes\Price;
use App\Models\Currency;

class DiscountBreakdownLine
{
    public function __construct(
        public Price $price,
        public string $identifier,
        public string $description,
        public ?float $percentage = null,
        public ?int $amount = null,
        public string $type = 'fixed', // 'fixed' or 'percentage'
    ) {
        //
    }

    /**
     * Create a fixed amount discount line
     */
    public static function fixed(
        int $amount,
        Currency $currency,
        string $identifier,
        string $description
    ): self {
        return new self(
            price: new Price($amount * 100, $currency),
            identifier: $identifier,
            description: $description,
            amount: $amount,
            percentage: null,
            type: 'fixed'
        );
    }

    /**
     * Create a percentage-based discount line
     */
    public static function percentage(
        int $baseAmount,
        float $percentage,
        Currency $currency,
        string $identifier,
        string $description
    ): self {
        $discountAmount = (int) ($baseAmount * ($percentage / 100));

        return new self(
            price: new Price($discountAmount, $currency),
            identifier: $identifier,
            description: $description,
            percentage: $percentage,
            amount: $discountAmount,
            type: 'percentage'
        );
    }

    /**
     * Check if this is a fixed discount
     */
    public function isFixed(): bool
    {
        return $this->type === 'fixed';
    }

    /**
     * Check if this is a percentage discount
     */
    public function isPercentage(): bool
    {
        return $this->type === 'percentage';
    }
}
