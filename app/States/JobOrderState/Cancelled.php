<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class Cancelled extends JobOrderState
{
    public static string $name = 'cancelled';

    public static string $permission = StaffPermission::MANAGE_JO_CANCELLED_TASKS;

    public static function getLabel(): ?string
    {
        return ucfirst(static::$name);
    }

    public static function getIcon(): ?string
    {
        return 'feather-x-circle';
    }

    public static function getColor(): ?string
    {
        return 'danger';
    }

    public static function getNext(): ?string
    {
        return null;
    }
}
