<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum InventoryType: string
{
    use Collectables;
    use HasOptions;
    use HasExclude;

    case FABRIC = 'fabric';
    case THREAD = 'thread';
    case PACKAGING = 'packaging';
    case SUBLI_PAPER = 'subli paper';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
