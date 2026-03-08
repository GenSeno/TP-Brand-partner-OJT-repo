<?php

namespace App\Enums;

use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum UserStatus: string
{
    use HasOptions;
    use HasExclude;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
