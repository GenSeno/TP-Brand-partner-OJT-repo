<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class HomepageReview extends Model
{
    protected $fillable = [
        'brand_partner_id',
        'name',
        'role',
        'comment',
        'avatar',
        'rating',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function brandPartner(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartner::class);
    }
}
