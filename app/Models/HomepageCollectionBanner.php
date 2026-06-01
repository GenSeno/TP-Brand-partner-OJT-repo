<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class HomepageCollectionBanner extends Model
{
    protected $fillable = [
        'brand_partner_id',
        'section_type',
        'title',
        'badge_text',
        'description',
        'image',
        'link_url',
        'link_text',
        'overlay_class',
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
