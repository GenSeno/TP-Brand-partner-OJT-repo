<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum InventoryMovementType: string
{
    use Collectables;
    use HasOptions;
    use HasExclude;

    case ADDITION = 'addition';
    case DEDUCTION = 'deduction';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ADDITION => 'success',
            self::DEDUCTION => 'danger',
        };
    }
}
