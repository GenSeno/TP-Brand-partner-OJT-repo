<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Models\Concerns\HasOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property bool $admin
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $email
 * @property string $password
 * @property UserStatus $status
 * @property string $remember_token
 * @property ?\Illuminate\Support\Carbon $email_verified_at
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Staff extends Authenticatable implements HasMedia
{
    use HasRoles;
    use HasOptions;
    use Notifiable;
    use InteractsWithMedia;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
    ];

    protected $guard_name = 'staff';

    protected $casts = [
        'admin' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => UserStatus::class,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar',
        'avatar_url',
        'full_name',
        'is_you',
        'current_role',
    ];

    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }

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

    protected function isYou(): Attribute
    {
        return Attribute::get(
            fn() => auth('staff')->check() && auth('staff')->id() === $this->id,
        );
    }

    protected function currentRole(): Attribute
    {
        return Attribute::get(
            fn() => $this->admin ? 'Super Admin' : $this->roles?->pluck('name')->first(),
        );
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value))
            return $query;

        return $query->where(function ($q) use ($value) {
            $searchable = ['first_name', 'last_name', 'email'];
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
