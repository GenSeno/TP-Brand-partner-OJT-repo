<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrandPartnerProductOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brand_partner_id',
        'name',
        'position',
    ];

    protected $casts = [
        'brand_partner_id' => 'integer',
        'position'         => 'integer',
    ];

    public function brandPartner(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartner::class);
    }

    public function values(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerProductOptionValue::class, 'product_option_id')
            ->orderBy('position');
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (! trim($value)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$value}%");
    }
}
