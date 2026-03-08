<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class Dispatching extends JobOrderState
{
    public static string $name = 'dispatching';
    public static string $permission = StaffPermission::MANAGE_JO_DISPATCHING_TASKS;

    public static function getLabel(): ?string
    {
        return ucfirst(static::$name);
    }

    public static function getIcon(): ?string
    {
        return 'feather-truck';
    }

    public static function getColor(): ?string
    {
        return 'dark';
    }

    public static function getNext(): ?string
    {
        return Completed::class;
    }
}
