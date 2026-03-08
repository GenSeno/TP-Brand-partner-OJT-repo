<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class Printing extends JobOrderState
{
    public static string $name = 'printing';

    public static string $permission = StaffPermission::MANAGE_JO_PRINTING_TASKS;

    public static function getLabel(): ?string
    {
        return ucfirst(static::$name);
    }

    public static function getIcon(): ?string
    {
        return 'feather-printer';
    }

    public static function getColor(): ?string
    {
        return 'warning';
    }

    public static function getNext(): ?string
    {
        return HeatPress::class;
    }
}
