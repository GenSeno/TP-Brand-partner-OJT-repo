<?php
namespace App\Models;

use App\Enums\EmployeeJobTitle;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property EmployeeJobTitle $job_title
 * @property UserStatus $status
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Employee extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'first_name',
        'last_name',
        'job_title',
        'status',
    ];

    protected $casts = [
        'job_title' => EmployeeJobTitle::class,
        'status' => UserStatus::class,
    ];

    protected $appends = [
        'avatar',
        'avatar_url',
        'full_name',
    ];

    protected function firstname(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['first_name'],
            set: fn(string $value) => ['first_name' => $value],
        );
    }

    protected function lastname(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['last_name'],
            set: fn(string $value) => ['last_name' => $value],
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn(): string => "{$this->first_name} {$this->last_name}",
        );
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMedia('avatar'),
        );
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMediaUrl('avatar', 'preview'),
        );
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value))
            return $query;

        return $query->where(function ($q) use ($value) {
            $searchable = ['first_name', 'last_name'];
            foreach ($searchable as $field) {
                $q->orWhere($field, 'like', "%{$value}%");
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->useFallbackUrl(asset('img/default.png'))
            ->useFallbackUrl(asset('img/default.png'), 'preview')
            ->useFallbackPath(public_path('img/default.png'))
            ->useFallbackPath(public_path('img/default.png'), 'preview')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('preview')
                    ->fit(Fit::Contain, 300, 300)
                    ->nonQueued();
            });
    }
}
