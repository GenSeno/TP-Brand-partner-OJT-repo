<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class Completed extends JobOrderState
{
    public static string $name = 'completed';

    public static string $permission = StaffPermission::MANAGE_JO_COMPLETED_TASKS;

    public static function getLabel(): ?string
    {
        return ucfirst(static::$name);
    }

    public static function getIcon(): ?string
    {
        return 'feather-check-circle';
    }

    public static function getColor(): ?string
    {
        return 'success';
    }

    public static function getNext(): ?string
    {
        return null;
    }
}
