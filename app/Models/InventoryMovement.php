<?php

namespace App\Models;

use App\Enums\InventoryMovementType;
use App\Observers\InventoryMovementObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $inventory_item_id
 * @property ?string $source_type
 * @property ?int $source_id
 * @property InventoryMovementType $type
 * @property bool $adjustment
 * @property float $amount
 * @property ?string $notes
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy(InventoryMovementObserver::class)]
class InventoryMovement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => InventoryMovementType::class,
        'adjustment' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function scopeAdjustment($query, $adjustment = true)
    {
        return $query->where('adjustment', $adjustment);
    }
}
