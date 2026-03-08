<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum UomType: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case COUNT = 'count';
    case WEIGHT = 'weight';
    case LENGTH = 'length';
    case VOLUME = 'volume';
    case AREA = 'area';
    case TIME = 'time';
    case CUSTOM = 'custom';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
