<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum BrandPartnerEventStatus: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case UPCOMING = 'upcoming';
    case ACTIVE = 'active';
    case ENDED = 'ended';

    public static function getDefault(): self
    {
        return self::UPCOMING;
    }

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::UPCOMING => 'info',
            self::ACTIVE => 'success',
            self::ENDED => 'secondary',
        };
    }
}
