<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class Artist extends JobOrderState
{
    public static string $name = 'artist';

    public static string $permission = StaffPermission::MANAGE_JO_ARTIST_TASKS;

    public static function getLabel(): ?string
    {
        return ucfirst(static::$name);
    }

    public static function getIcon(): ?string
    {
        return 'feather-edit-3';
    }

    public static function getColor(): ?string
    {
        return 'info';
    }

    public static function getNext(): ?string
    {
        return Approval::class;
    }
}
