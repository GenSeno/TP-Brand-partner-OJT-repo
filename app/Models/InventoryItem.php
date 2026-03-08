<?php

namespace App\Models;

use App\Enums\InventoryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\HasOptions;
/**
 * @property int $id
 * @property InventoryType $type
 * @property string $item_name
 * @property float $current_stock
 * @property string $uom_code
 * @property ?string $notes
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class InventoryItem extends Model
{   
    use HasOptions;
    
    protected $guarded = [];

    protected $casts = [
        'type' => InventoryType::class,
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'uom_code', 'code');
    }

    public function jobOrderProducts(): HasMany
    {
        return $this->hasMany(JobOrderProduct::class);
    }

    public function jobOrders()
    {
        return $this->hasMany(JobOrderInventoryItem::class);
    }

    public function scopeType(Builder $query, InventoryType $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('current_stock', '>', 0);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('item_name', 'like', "%{$search}%")
            ->orWhere('notes', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%");
    }
}
