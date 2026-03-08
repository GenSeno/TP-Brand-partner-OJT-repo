<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum PurchaseOrderStatus: string
{
    use Collectables;
    use HasOptions;
    use HasExclude;

    case DRAFT = 'draft';
    case SENT = 'sent';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'warning',
            self::SENT => 'info',
            self::RECEIVED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
