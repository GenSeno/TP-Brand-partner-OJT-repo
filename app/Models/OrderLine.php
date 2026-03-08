<?php

namespace App\Models;

use App\Enums\OrderLineType;
use App\Lunar\Casts\Price;
use App\Lunar\Casts\TaxBreakdown;
use App\Lunar\Traits\LogsActivity;
use App\Observers\OrderLineObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $order_id
 * @property string $purchasable_type
 * @property int $purchasable_id
 * @property OrderLineType $type
 * @property string $description
 * @property ?string $option
 * @property string $identifier
 * @property int $unit_price
 * @property int $unit_quantity
 * @property int $quantity
 * @property int $sub_total
 * @property int $discount_total
 * @property array $tax_breakdown
 * @property int $tax_total
 * @property int $total
 * @property ?string $notes
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy([OrderLineObserver::class])]
class OrderLine extends Model implements Contract\OrderLinePrintable
{
    /** @use HasFactory<\Database\Factories\OrderLineFactory> */
    use HasFactory;
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'meta' => AsArrayObject::class,
        'tax_breakdown' => TaxBreakdown::class,
        'unit_price' => Price::class,
        'sub_total' => Price::class,
        'tax_total' => Price::class,
        'discount_total' => Price::class,
        'total' => Price::class,
        'type' => OrderLineType::class,
    ];

    public function order(): Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function purchasable(): Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function currency(): Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Currency::class,
            Order::class,
            'id',
            'code',
            'order_id',
            'currency_code'
        );
    }

    public function productions(): Relations\HasMany
    {
        return $this->hasMany(JobOrderProduction::class, 'order_line_id');
    }

    public function assignments(): Relations\HasMany
    {
        return $this->hasMany(JobOrderAssignment::class, 'order_line_id');
    }
}
