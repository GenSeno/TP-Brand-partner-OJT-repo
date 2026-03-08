<?php

namespace App\Lunar\ValueObjects\Cart;

use App\Models\Currency;
use Illuminate\Support\Collection;

class DiscountBreakdown
{
    public function __construct(
        public ?Collection $amounts = null
    ) {
        $this->amounts = $amounts ?: collect();
    }

    /**
     * Add a discount breakdown line.
     *
     * @return void
     */
    public function addAmount(DiscountBreakdownLine $discountBreakdownLine)
    {
        $this->amounts->push($discountBreakdownLine);
    }

    /**
     * Add a fixed amount discount
     *
     * @return $this
     */
    public function addFixed(
        int $amount,
        Currency $currency,
        string $identifier,
        string $description
    ): self {
        $this->addAmount(
            DiscountBreakdownLine::fixed($amount, $currency, $identifier, $description)
        );

        return $this;
    }

    /**
     * Add a percentage-based discount
     *
     * @return $this
     */
    public function addPercentage(
        int $baseAmount,
        float $percentage,
        Currency $currency,
        string $identifier,
        string $description
    ): self {
        $this->addAmount(
            DiscountBreakdownLine::percentage($baseAmount, $percentage, $currency, $identifier, $description)
        );

        return $this;
    }

    /**
     * Get total discount amount
     *
     * @return int
     */
    public function total(): int
    {
        return $this->amounts->sum(fn($discount) => $discount->price->value);
    }

    /**
     * Get only fixed discounts
     *
     * @return Collection
     */
    public function fixed(): Collection
    {
        return $this->amounts->filter(fn($discount) => $discount->isFixed());
    }

    /**
     * Get only percentage discounts
     *
     * @return Collection
     */
    public function percentage(): Collection
    {
        return $this->amounts->filter(fn($discount) => $discount->isPercentage());
    }
}
