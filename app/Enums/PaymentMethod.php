<?php

namespace App\Enums;

use App\Enums\Traits\Collectables;
use App\Enums\Traits\HasExclude;
use App\Enums\Traits\HasOptions;

enum PaymentMethod: string
{
    use Collectables;
    use HasExclude;
    use HasOptions;

  //  case CREDIT_CARD = 'credit_card';
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank';
  //  case PAYPAL = 'paypal';
    case GCASH = 'gcash';
    case CHECK = 'check';

    public function getLabel(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CHECK => 'Check',
            self::CASH => 'Cash',
            self::GCASH => 'Gcash',
            
        };
    }

     public static function groups(): array
    {
        return [
            'cash' => [
                self::CASH->value,
            ],
            'bank' => [
                self::BANK_TRANSFER->value,
                self::CHECK->value,
            ],
            'gcash' => [
                self::GCASH->value,
            ],
        ];
    }

    public static function groupLabels(): array
    {
        return [
            'cash' => 'Cash',
            'bank' => 'Bank',
            'gcash' => 'GCash',
        ];
    }

    public static function getDropdown(): array
    {
        return [
            self::BANK_TRANSFER->value => 'Bank Transfer',
            self::CHECK->value => 'Check',
            self::CASH->value => 'Cash',
            self::GCASH->value => 'Gcash',
            
        ];
    }
}
