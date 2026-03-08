<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum BrandPartnerProductStatus: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case DISABLED = 'disabled';

    public static function getDefault(): self
    {
        return self::DRAFT;
    }

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'warning',
            self::PUBLISHED => 'success',
            self::DISABLED => 'secondary',
        };
    }
}
