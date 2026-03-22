<?php

namespace App\Models;

use App\Enums\BrandPartnerStatus;
use App\Lunar\Traits\LogsActivity;
use App\Observers\BrandPartnerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $email
 * @property string $password
 * @property ?string $contact_person
 * @property ?string $phone
 * @property ?string $address
 * @property ?string $logo
 * @property ?string $description
 * @property BrandPartnerStatus $status
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $email_verified_at
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
#[ObservedBy(BrandPartnerObserver::class)]
class BrandPartner extends Authenticatable implements HasMedia
{
    use HasFactory;
    use Notifiable;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $guard_name = 'brand_partner';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'contact_person',
        'phone',
        'address',
        'logo',
        'description',
        'status',
        'meta',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => BrandPartnerStatus::class,
        'meta' => AsArrayObject::class,
    ];

    protected $appends = [
        'logo_url',
    ];

    // Relationships
    public function categories(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerCategory::class);
    }

    public function events(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerEvent::class);
    }

    public function products(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerProduct::class);
    }

    public function orders(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerOrder::class);
    }

    public function productOptions(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerProductOption::class);
    }

    public function collections(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerCollection::class);
    }

    public function sizes(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerSize::class);
    }

    // Accessors
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMediaUrl('logo', 'preview'),
        );
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', BrandPartnerStatus::ACTIVE);
    }

    public function scopeStatus(Builder $query, BrandPartnerStatus|string $status): Builder
    {
        $status = $status instanceof BrandPartnerStatus ? $status : BrandPartnerStatus::from($status);
        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value)) {
            return $query;
        }

        return $query->where(function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%")
              ->orWhere('email', 'like', "%{$value}%")
              ->orWhere('contact_person', 'like', "%{$value}%");
        });
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === BrandPartnerStatus::ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->status === BrandPartnerStatus::PENDING;
    }

    public function isSuspended(): bool
    {
        return $this->status === BrandPartnerStatus::SUSPENDED;
    }

    // Media
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
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
