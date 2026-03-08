<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum InvoiceLineType: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case PRODUCT = 'product';
    case SHIPPING = 'shipping';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
