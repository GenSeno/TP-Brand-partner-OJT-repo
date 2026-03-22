<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $brand_partner_id
 * @property string $name
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class BrandPartnerSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_partner_id',
        'name',
    ];

    protected $casts = [
        'brand_partner_id' => 'integer',
    ];

    // Relationships
    public function brandPartner(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartner::class);
    }

    // Scopes
    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$value}%");
    }
}
