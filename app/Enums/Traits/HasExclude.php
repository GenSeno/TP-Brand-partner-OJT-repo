<?php

namespace App\Enums\Traits;

trait HasExclude
{
    public static function exclude(...$items)
    {
        return array_values(array_filter(
            self::cases(),
            fn($case) => !in_array($case, $items, true)
            && !in_array($case->value, $items, true)
        ));
    }

}
