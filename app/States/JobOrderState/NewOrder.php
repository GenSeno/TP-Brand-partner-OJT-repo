<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class NewOrder extends JobOrderState
{
    public static string $name = 'new';

    public static string $permission = StaffPermission::MANAGE_JO_NEW_ORDER_TASKS;

    public static function getLabel(): ?string
    {
        return ucfirst(static::$name);
    }

    public static function getIcon(): ?string
    {
        return 'feather-plus';
    }

    public static function getColor(): ?string
    {
        return 'primary';
    }

    public static function getNext(): ?string
    {
        return Artist::class;
    }
}
