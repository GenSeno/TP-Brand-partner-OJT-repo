<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class HomepageSlider extends Model
{
    protected $fillable = [
        'brand_partner_id',
        'title',
        'subtitle',
        'image',
        'link_url',
        'link_text',
        'overlay_type',
        'content_position',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function brandPartner(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartner::class);
    }
}
