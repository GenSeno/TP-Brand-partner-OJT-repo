<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum JobOrderUrgency: int
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case RUSH = 1;
    case PRIORITY = 2;
    case NORMAL = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            JobOrderUrgency::RUSH => "Rush",
            JobOrderUrgency::PRIORITY => "Priority",
            JobOrderUrgency::NORMAL => "Normal",
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            JobOrderUrgency::RUSH => 'danger',
            JobOrderUrgency::PRIORITY => 'warning',
            JobOrderUrgency::NORMAL => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            JobOrderUrgency::RUSH => "\u{1F534}",
            JobOrderUrgency::PRIORITY => "\u{1F7E1}",
            JobOrderUrgency::NORMAL => "\u{1F7E2}",
        };
    }
}
