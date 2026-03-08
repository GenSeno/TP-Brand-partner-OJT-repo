<?php

namespace App\Models;

use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $product_name
 * @property int $quantity
 * @property int $unit_price
 * @property int $total
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class BrandPartnerOrderLine extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total',
        'meta',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'total' => 'integer',
        'meta' => AsArrayObject::class,
    ];

    protected $appends = [
        'formatted_unit_price',
        'formatted_total',
    ];

    // Relationships
    public function order(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartnerOrder::class, 'order_id');
    }

    public function product(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartnerProduct::class, 'product_id');
    }

    // Accessors
    protected function formattedUnitPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->unit_price / 100, 2),
        );
    }

    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->total / 100, 2),
        );
    }

    // Boot
    protected static function booted(): void
    {
        static::creating(function (BrandPartnerOrderLine $line) {
            $line->total = $line->unit_price * $line->quantity;
        });

        static::updating(function (BrandPartnerOrderLine $line) {
            $line->total = $line->unit_price * $line->quantity;
        });

        static::saved(function (BrandPartnerOrderLine $line) {
            $line->order->recalculate();
        });

        static::deleted(function (BrandPartnerOrderLine $line) {
            $line->order->recalculate();
        });
    }
}
