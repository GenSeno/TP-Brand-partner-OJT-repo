<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum ProductStatus: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    // case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case DISABLED = 'disabled';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
