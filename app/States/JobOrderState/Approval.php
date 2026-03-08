<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class Approval extends JobOrderState
{
    public static string $name = 'approval';

    public static string $permission = StaffPermission::MANAGE_JO_APPROVAL_TASKS;

    public static function getLabel(): ?string
    {
        return ucfirst(static::$name);
    }

    public static function getIcon(): ?string
    {
        return 'feather-thumbs-up';
    }

    public static function getColor(): ?string
    {
        return 'info';
    }

    public static function getNext(): ?string
    {
        return Printing::class;
    }
}
