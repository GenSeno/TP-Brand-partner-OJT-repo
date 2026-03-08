<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum PersonTitle: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case MR = 'Mr.';
    case MRS = 'Mrs.';
    case MS = 'Ms.';
    case DR = 'Dr.';
    case PROF = 'Prof.';
    case REV = 'Rev.';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
