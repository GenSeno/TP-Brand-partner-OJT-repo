<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class BrandPartnerProductOptionValue extends Model
{
    protected $fillable = [
        'product_option_id',
        'label',
        'value',
        'position',
    ];

    protected $casts = [
        'product_option_id' => 'integer',
        'position'          => 'integer',
    ];

    public function option(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartnerProductOption::class, 'product_option_id');
    }
}
