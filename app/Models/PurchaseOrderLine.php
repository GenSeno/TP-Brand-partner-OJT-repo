<?php

namespace App\Models;

use App\Observers\PurchaseOrderLineObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Lunar\Casts\Price;

/**
 * @property int $id
 * @property int $purchase_order_id
 * @property string $description
 * @property int $unit_price
 * @property int $unit_quantity
 * @property int $quantity
 * @property int $total
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy([PurchaseOrderLineObserver::class])]
class PurchaseOrderLine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'unit_price' => Price::class,
        'total' => Price::class,
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
