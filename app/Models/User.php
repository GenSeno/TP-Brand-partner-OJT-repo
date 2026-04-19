<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property ?\Illuminate\Support\Carbon $email_verified_at
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar',
        'avatar_url',
        'is_you',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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

    protected function isYou(): Attribute
    {
        return Attribute::get(
            fn() => auth('web')->check() && auth('web')->id() === $this->id,
        );
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value))
            return $query;

        return $query->where(function ($q) use ($value) {
            $searchable = ['name', 'email'];
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

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
