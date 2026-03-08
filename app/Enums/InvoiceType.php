<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum InvoiceType: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case DOWN_PAYMENT = 'down-payment';
    case INSTALLMENT = 'installment';
    case FINAL_PAYMENT = 'final-payment';

    public function getLabel(): string
    {
        return match ($this) {
            self::DOWN_PAYMENT => 'Down Payment',
            self::INSTALLMENT => 'Installment',
            self::FINAL_PAYMENT => 'Final Payment',
        };
    }
}
