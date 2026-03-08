<?php

namespace App\Models;

use App\Enums\BrandPartnerEventStatus;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $brand_partner_id
 * @property string $name
 * @property string $slug
 * @property ?string $description
 * @property ?\Illuminate\Support\Carbon $start_date
 * @property ?\Illuminate\Support\Carbon $end_date
 * @property BrandPartnerEventStatus $status
 * @property bool $enabled
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class BrandPartnerEvent extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'brand_partner_id',
        'name',
        'slug',
        'description',
        'start_date',
        'end_date',
        'status',
        'enabled',
        'meta',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => BrandPartnerEventStatus::class,
        'enabled' => 'boolean',
        'meta' => AsArrayObject::class,
    ];

    // Relationships
    public function brandPartner(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartner::class);
    }

    public function products(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerProduct::class, 'event_id');
    }

    // Scopes
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', BrandPartnerEventStatus::ACTIVE);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', BrandPartnerEventStatus::UPCOMING);
    }

    public function scopeStatus(Builder $query, BrandPartnerEventStatus|string $status): Builder
    {
        $status = $status instanceof BrandPartnerEventStatus ? $status : BrandPartnerEventStatus::from($status);
        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$value}%");
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === BrandPartnerEventStatus::ACTIVE;
    }

    public function isUpcoming(): bool
    {
        return $this->status === BrandPartnerEventStatus::UPCOMING;
    }

    public function isEnded(): bool
    {
        return $this->status === BrandPartnerEventStatus::ENDED;
    }
}
