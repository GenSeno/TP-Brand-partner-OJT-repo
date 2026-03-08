<?php

namespace App\Models;

use App\Models\Concerns\HasOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property bool $shared
 * @property bool $autoapply
 * @property int $position
 * @property bool $permanent
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class ProductOption extends Model
{
    use HasOptions;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'shared' => 'boolean',
        'autoapply' => 'boolean',
    ];

    protected $appends = [
        'pluralized_name',
    ];

    public function scopeShared(Builder $builder, $shared = true): Builder
    {
        return $builder->where('shared', '=', $shared);
    }

    public function scopeAutoApplied(Builder $builder, $autoapply = true): Builder
    {
        return $builder->where('autoapply', '=', $autoapply);
    }

    public function scopeExclusive(Builder $builder): Builder
    {
        return $builder->where('shared', '=', false);
    }

    public function scopePermanent(Builder $builder, $permanent = true): Builder
    {
        return $builder->where('permanent', '=', $permanent);
    }

    public function values(): Relations\HasMany
    {
        return $this->hasMany(ProductOptionValue::class)
            ->orderBy('position');
    }

    public function products(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_option_product')
            ->withPivot(['position'])
            ->orderByPivot('position');
    }

    public function pluralizedName(): Attribute
    {
        return Attribute::get(fn() => str($this->name)->plural());
    }
}
