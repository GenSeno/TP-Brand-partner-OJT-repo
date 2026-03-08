<?php

namespace App\Models;

use App\Lunar\Traits\LogsActivity;
use App\Models\Concerns\HasOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string $contact_person
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $city
 * @property string $province
 * @property string $postcode
 * @property bool $enabled
 * @property string $notes
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class Supplier extends Model implements HasMedia
{
    use HasOptions;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

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

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMedia('logo'),
        );
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value))
            return $query;

        return $query->where(function ($q) use ($value) {
            foreach (['name', 'contact_person', 'email'] as $field) {
                $q->orWhere($field, 'like', "%{$value}%");
            }
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

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeOrderByName(Builder $query, $direction = 'asc'): Builder
    {
        return $query->orderBy('name', $direction);
    }
}
