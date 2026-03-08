<?php

namespace App\States\Traits;

use Illuminate\Support\Collection;

/**
 * @mixin \Spatie\ModelStates\State
 */
trait HasSorting
{
    abstract public static function getOrder(): array;

    public static function allOrdered(): Collection
    {
        return static::all()->sortBy(
            fn($class, $key) =>
            array_search($key, static::getOrder(), true)
        );
    }
}
