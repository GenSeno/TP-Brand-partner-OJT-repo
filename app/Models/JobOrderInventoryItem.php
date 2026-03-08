<?php

namespace App\Models;

use App\Observers\JobOrderInventoryItemObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $job_order_id
 * @property int $inventory_item_id
 * @property string|null $stage
 * @property float $amount_used
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy(JobOrderInventoryItemObserver::class)]
class JobOrderInventoryItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount_used' => 'float',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
