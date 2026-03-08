<?php

namespace App\Lunar\ValueObjects\Cart;

use App\Lunar\DataTypes\Price;

class TaxBreakdownAmount
{
    public function __construct(
        public Price $price,
        public string $identifier,
        public string $description,
        public float $percentage,
    ) {
        //
    }
}
