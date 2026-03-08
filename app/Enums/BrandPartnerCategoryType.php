<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum BrandPartnerCategoryType: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case REGULAR = 'regular';
    case EVENT = 'event';

    public static function getDefault(): self
    {
        return self::REGULAR;
    }

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::REGULAR => 'primary',
            self::EVENT => 'info',
        };
    }
}
