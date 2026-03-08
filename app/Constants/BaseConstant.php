<?php

namespace App\Constants;

use Illuminate\Support\Collection;

abstract class BaseConstant
{
    public static function all(): Collection
    {
        return collect((new \ReflectionClass(static::class))->getConstants());
    }

    public static function names(): Collection
    {
        return static::all()->keys();
    }

    public static function values(): Collection
    {
        return static::all()->values();
    }

    public static function with(...$keys): Collection
    {
        return static::all()->map(function ($value, $key) use ($keys) {
            $attr = [];
            foreach ($keys as $allowedKey) {
                $attr[$allowedKey] = static::{'get' . ucfirst($allowedKey)}($value);
            }
            return [
                'name' => $key,
                'value' => $value,
                ...$attr,
            ];
        })->filter();
    }
}
