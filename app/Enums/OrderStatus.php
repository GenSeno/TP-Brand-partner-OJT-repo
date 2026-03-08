<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum OrderStatus: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case PENDING = 'pending';
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case PARTIALLY_PAID = 'partially-paid';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function getDefault(): self
    {
        return self::PENDING;
    }

    public function getLabel(): string
    {
        return ucfirst(str_replace('-', ' ', $this->value));
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::UNPAID => 'danger',
            self::PARTIALLY_PAID => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'secondary',
        };
    }

    public function getMailers(): array
    {
        return match ($this) {
            self::PENDING => [],
            self::UNPAID => [],
            self::PARTIALLY_PAID => [],
            self::COMPLETED => [],
            self::CANCELLED => [],
        };
    }

    public function getNotifications(): array
    {
        return match ($this) {
            self::PENDING => [],
            self::UNPAID => [],
            self::PARTIALLY_PAID => [],
            self::COMPLETED => [],
            self::CANCELLED => [],
        };
    }
}
