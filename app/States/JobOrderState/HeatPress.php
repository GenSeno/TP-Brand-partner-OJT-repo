<?php

namespace App\States\JobOrderState;

use App\Constants\StaffPermission;

class HeatPress extends JobOrderState
{
    public static string $name = 'heatpress';

    public static string $permission = StaffPermission::MANAGE_JO_HEAT_PRESS_TASKS;

    public static function getLabel(): ?string
    {
        return 'Heat Press/Cutting';
    }

    public static function getIcon(): ?string
    {
        return 'feather-zap';
    }

    public static function getColor(): ?string
    {
        return 'danger';
    }

    public static function getNext(): ?string
    {
        return Sewing::class;
    }
}
