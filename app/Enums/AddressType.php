<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;

enum AddressType: string
{
    use HasExclude;

    // Address types for quotes, customers and general addresses
    case SHIPPING = 'shipping';
    case BILLING = 'billing';

    // Address types (label) for users
    case HOME = 'home';
    case OFFICE = 'office';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    public static function getGeneralType(): array
    {
        return [
            self::SHIPPING,
            self::BILLING,
        ];
    }

    public static function getLabelType(): array
    {
        return [
            self::HOME,
            self::OFFICE,
        ];
    }
}
