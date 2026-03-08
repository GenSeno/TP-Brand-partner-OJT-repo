<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class Packing extends JobOrderState
{
    public static string $name = 'packing';

    public static string $permission = StaffPermission::MANAGE_JO_PACKING_TASKS;

    public static function getLabel(): ?string
    {
        return ucfirst(static::$name);
    }

    public static function getIcon(): ?string
    {
        return 'feather-package';
    }

    public static function getColor(): ?string
    {
        return 'success';
    }

    public static function getNext(): ?string
    {
        return Dispatching::class;
    }
}
