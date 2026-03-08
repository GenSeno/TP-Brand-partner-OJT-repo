<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $product_option_id
 * @property string $label
 * @property string $value
 * @property int $position
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class ProductOptionValue extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function option(): Relations\BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function variants(): Relations\BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            foreignPivotKey: 'value_id',
            relatedPivotKey: 'variant_id',
        );
    }

}
