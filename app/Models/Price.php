<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lunar\Casts\Price as CastsPrice;
use App\Lunar\DataTypes\Price as PriceDataType;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $currency_id
 * @property string $pricable_type
 * @property int $pricable_id
 * @property PriceDataType|int $amount
 * @property ?PriceDataType|int $compare_amount
 * @property int $min_quantity
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Price extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => CastsPrice::class,
        'compare_amount' => CastsPrice::class,
    ];

    public function pricable()
    {
        return $this->morphTo();
    }

    public function currency(): Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
