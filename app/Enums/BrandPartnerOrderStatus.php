<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum BrandPartnerOrderStatus: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case SENT_TO_TPINKLAB = 'sent_to_tpinklab';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function getDefault(): self
    {
        return self::PENDING;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SENT_TO_TPINKLAB => 'Sent to TPInkLab',
            default => ucfirst($this->value),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'primary',
            self::SENT_TO_TPINKLAB => 'info',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
