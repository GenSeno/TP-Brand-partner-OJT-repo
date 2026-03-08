<?php

namespace App\Constants\Traits;

use Illuminate\Support\Collection;

trait HasGroups
{
    public static function groups(): Collection
    {
        $groups = [];
        static::all()->each(function ($constant) use (&$groups) {
            $group = explode(':', $constant)[0];
            if (!in_array($group, $groups)) {
                $groups[] = $group;
            }
        });
        return collect($groups);
    }

    public static function constantsByGroup(string $group): Collection
    {
        return static::all()->filter(function ($constant) use ($group) {
            return explode(':', $constant)[0] === $group;
        })->values();
    }
}
