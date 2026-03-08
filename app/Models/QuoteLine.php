<?php

namespace App\Models;

use App\Lunar\DataTypes\Price;
use App\Lunar\Traits\LogsActivity;
use App\Lunar\Casts\Price as PriceObject;
use App\Observers\QuoteLineObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $quote_id
 * @property int $product_id
 * @property string $purchasable_type
 * @property int $purchasable_id
 * @property int $quantity
 * @property Price $purchase_price
 * @property Price $discount_total
 * @property float $tax_rate
 * @property Price $tax_total
 * @property Price $unit_cost
 * @property Price $total
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy([QuoteLineObserver::class])]
class QuoteLine extends Model
{
    use LogsActivity;

    protected $guarded = [];

    protected $fillable = [
        'quotation_id',
        'purchasable_type',
        'purchasable_id',
        'quantity',
        'purchase_price',
        'meta',
        'total'
    ];

    protected $casts = [
        'meta' => AsArrayObject::class,
        'purchase_price' => PriceObject::class,
        'discount_total' => PriceObject::class,
        'tax_total' => PriceObject::class,
        'total' => PriceObject::class,
    ];

    protected $attributes = [
     'discount_total' => 0,
     'tax_rate' => 0,
     'tax_total' => 0,
     'unit_cost' => 0,
     'total' => 0,
    ];

    public function quote(): Relations\BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function purchasable(): Relations\MorphTo
    {
        return $this->morphTo();
    } 
}
