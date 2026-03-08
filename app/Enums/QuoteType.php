<?php

namespace App\Enums;

use App\Enums\Traits\HasOptions;

/**
 * - OFFLINE: Prepared by staff for offline use
 * - ONLINE: Generated for online use by customer
 */
enum QuoteType: string
{
    use HasOptions;

    case OFFLINE = 'offline';
    case ONLINE = 'online';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
