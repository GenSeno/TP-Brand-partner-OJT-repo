<?php

namespace App\Enums;

use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

/**
 * - DRAFT: 
 * - UPCOMING: 
 * - PAID:
 * - CANCELLED: 
 */
enum ExpenseStatus: string
{
    use HasOptions;
    use HasExclude;

    case DRAFT = 'draft'; 
    case UPCOMING = 'upcoming'; 
    case PAID = 'paid';
    case CANCELLED = 'cancelled'; 

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

     public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::UPCOMING => 'primary',
            self::PAID => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
