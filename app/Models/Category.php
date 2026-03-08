<?php

namespace App\Models;

use App\Models\Concerns\HasOptions;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $enabled
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Category extends Model implements HasMedia
{
    use HasOptions;
    use InteractsWithMedia;
    use LogsActivity;

    protected $guarded = [];

    protected $appends = [
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMedia('logo'),
        );
    }

    public function products(): Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value))
            return $query;

        return $query->where(function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%")
                ->orWhere('slug', 'like', "%{$value}%");
        });
    }

    public function scopeEnabled(Builder $query, bool $enabled = true): Builder
    {
        return $query->where('enabled', $enabled);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
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
