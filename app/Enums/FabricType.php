<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum FabricType: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case RIBSTOP_110GSM = 'Ribstop 110gsm';
    case POLYDEX_150GSM = 'Polydex 150gsm';
    case CM_STRIPE_130GSM = 'Cm stripe 130gsm';
    case PINEAPPLE = 'Pineapple';
    case MICROOL_110GSM_PREMIUM = 'Microol 110gsm premium';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
