<?php

namespace App\Enums;

use App\Enums\Traits\HasOptions;

/**
 * - PHYSICAL: Deliverable logistic products
 * - DIGITAL: Stockless digital services
 * - SHIPPING: Shipping method cost
 */
enum OrderLineType: string
{
    use HasOptions;

    case PHYSICAL = 'physical';
    case DIGITAL = 'digital';
    case SHIPPING = 'shipping';

    public function getLabel(): ?string
    {
        return ucfirst($this->value);
    }
}
