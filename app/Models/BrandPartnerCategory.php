<?php

namespace App\Models;

use App\Enums\BrandPartnerCategoryType;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $brand_partner_id
 * @property string $name
 * @property string $slug
 * @property BrandPartnerCategoryType $type
 * @property bool $enabled
 * @property int $position
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class BrandPartnerCategory extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'brand_partner_id',
        'name',
        'slug',
        'notes',
        'type',
        'enabled',
        'position',
    ];

    protected $casts = [
        'type' => BrandPartnerCategoryType::class,
        'enabled' => 'boolean',
        'position' => 'integer',
    ];

    // Relationships
    public function brandPartner(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartner::class);
    }

    public function products(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerProduct::class, 'category_id');
    }

    // Scopes
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function scopeRegular(Builder $query): Builder
    {
        return $query->where('type', BrandPartnerCategoryType::REGULAR);
    }

    public function scopeEvent(Builder $query): Builder
    {
        return $query->where('type', BrandPartnerCategoryType::EVENT);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position');
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$value}%");
    }

    // Methods
    public function isEventType(): bool
    {
        return $this->type === BrandPartnerCategoryType::EVENT;
    }

    public function isRegularType(): bool
    {
        return $this->type === BrandPartnerCategoryType::REGULAR;
    }
}
