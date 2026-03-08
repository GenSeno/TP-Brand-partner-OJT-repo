<?php

namespace App\Enums\Traits;

use Illuminate\Support\Collection;

trait Collectables
{
    public static function collection($cases = 'cases', ...$attr): Collection
    {
        return collect(self::{$cases}(...$attr));
    }
}
