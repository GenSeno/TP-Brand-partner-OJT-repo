<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Lunar\Traits\LogsActivity;
use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property ?int $category_id
 * @property string $name
 * @property ?string $description
 * @property ProductStatus $status
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
#[ObservedBy(ProductObserver::class)]
class Product extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'image',
    ];

    protected $casts = [
        'meta' => AsArrayObject::class,
        'status' => ProductStatus::class,
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMedia('image'),
        );
    }

    public function variants(): Relations\HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function options(): Relations\BelongsToMany
    {
        return $this->belongsToMany(ProductOption::class, 'product_option_product')
            ->withPivot(['position'])
            ->orderByPivot('position');
    }

    public function category(): Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeStatus(Builder $query, ProductStatus|string $status): Builder
    {
        $status = $status instanceof ProductStatus ? $status : ProductStatus::from($status);
        return $query->whereStatus($status);
    }

    public function prices()
    {
        return Price::whereIn('pricable_id', $this->variants->pluck('id'))
            ->where('pricable_type', ProductVariant::class)
            ->get();
    }

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->variant?->price,
        );
    }

    public function productOptions(): Relations\BelongsToMany
    {
        return $this->belongsToMany(ProductOption::class, 'product_option_product')
            ->withPivot(['position'])
            ->orderByPivot('position');
    }

    public function toggleStatus()
    {
        // if ($this->status->is(ProductStatus::DRAFT)) {
        //     return false;
        // }

        $this->status = $this->status->is(ProductStatus::DISABLED)
            ? ProductStatus::PUBLISHED
            : ProductStatus::DISABLED;

        return $this->save();
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value))
            return $query;

        return $query->where(function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%");
        });
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->useFallbackUrl(asset('img/brand/brand-icon-01.png'))
            ->useFallbackUrl(asset('img/brand/brand-icon-01.png'), 'preview')
            ->useFallbackPath(public_path('img/brand/brand-icon-01.png'))
            ->useFallbackPath(public_path('img/brand/brand-icon-01.png'), 'preview')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('preview')
                    ->fit(Fit::Contain, 300, 300)
                    ->nonQueued();
            });
    }
    
}
