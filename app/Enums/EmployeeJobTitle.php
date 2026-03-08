<?php
namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum EmployeeJobTitle: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

    case MANAGEMENT = 'management';
    case ARTIST = 'artist';
    case PRINTER_OPERATOR = 'printer operator';
    case HEAT_PRESS = 'heat press';
    case SEWER = 'sewer';
    case QA = 'qa';
    case DISPATCHER = 'dispatcher';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MANAGEMENT => 'Management',
            self::ARTIST => 'Artist',
            self::PRINTER_OPERATOR => 'Printer Operator',
            self::HEAT_PRESS => 'Heat Press',
            self::SEWER => 'Sewer',
            self::QA => 'Quality Assurance',
            self::DISPATCHER => 'Dispatcher',
        };
    }
}
