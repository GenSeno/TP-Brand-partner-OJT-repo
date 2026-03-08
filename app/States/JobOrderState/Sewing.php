<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class Sewing extends JobOrderState
{
    public static string $name = 'sewing';

    public static string $permission = StaffPermission::MANAGE_JO_SEWING_TASKS;

    public static function getLabel(): ?string
    {
        return 'Sewing';
    }

    public static function getIcon(): ?string
    {
        return 'feather-scissors';
    }

    public static function getColor(): ?string
    {
        return 'secondary';
    }

    public static function getNext(): ?string
    {
        return Packing::class;
    }
}
