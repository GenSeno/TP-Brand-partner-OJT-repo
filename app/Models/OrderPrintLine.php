<?php

namespace App\Models;

use App\Lunar\Casts\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $printable_type
 * @property int $printable_id
 * @property int $product_id
 * @property string $product_name
 * @property string $sku
 * @property string $uom_code
 * @property int $unit_price
 * @property int $quantity
 * @property int $total
 * @property array $options_payload
 * @property int $sort_order
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class OrderPrintLine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'options_payload' => 'array',
        'unit_price' => Price::class,
        'total' => Price::class,
    ];

    public function printable(): MorphTo
    {
        return $this->morphTo();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'uom_code', 'code');
    }
}
