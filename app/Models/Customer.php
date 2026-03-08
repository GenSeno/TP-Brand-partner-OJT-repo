<?php

namespace App\Models;

use App\Lunar\Traits\LogsActivity;
use App\Models\Concerns\HasOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property ?string $title
 * @property string $first_name
 * @property string $last_name
 * @property ?string $company_name
 * @property ?string $vat_no
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Customer extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use SoftDeletes;
    use HasFactory;
    use HasOptions;
    use LogsActivity;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'company_name',
        'enabled'
    ];

    protected $guarded = [];
    protected $dates = ['deleted_at'];

    protected $casts = [
        'meta' => AsArrayObject::class,
        'enabled' => 'boolean'
    ];

    protected $appends = ['avatar_url', 'has_avatar', 'full_name', 'is_deletable', 'billing', 'shipping'];

    public function users(): Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function addresses(): Relations\MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function invoices(): Relations\HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class, Order::class, 'orderable_id', 'order_id')
            ->where('orderable_type', self::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('customers')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg'])
            ->useDisk('public');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('customers');
    }

    public function getHasAvatarAttribute(): bool
    {
        return $this->hasMedia('customers');
    }

    public function getFullNameAttribute(): string
    {
        $name = trim("{$this->first_name} {$this->last_name}");

        if ($this->title) {
            return "{$this->title} {$name}";
        }

        return $name;
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeDisabled($query)
    {
        return $query->where('enabled', false);
    }

    // Relationship to get default address
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('default', true);
    }

    public function scopeSearch($query, $term)
    {
        if (!$term)
            return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('company_name', 'like', "%{$term}%")
                ->orWhereHas('addresses', function ($q2) use ($term) {
                    $q2->where('default', 1)
                        ->where(function ($q3) use ($term) {
                            $q3->where('email', 'like', "%{$term}%")
                                ->orWhere('phone', 'like', "%{$term}%")
                                ->orWhere('province', 'like', "%{$term}%");
                        });
                });
        });
    }

    protected static function booted()
    {
        static::deleting(function ($customer) {
            if ($customer->isForceDeleting()) {
                // Permanently delete addresses
                $customer->addresses()->forceDelete();
            } else {
                // Soft delete addresses
                $customer->addresses()->delete();
            }
        });

        static::restoring(function ($customer) {
            // Restore addresses when customer is restored
            $customer->addresses()->withTrashed()->restore();
        });
    }

    public function billing()
    {
        return $this->addresses()
            ->where('default', 1)
            ->where('type', 'billing');
    }

    public function shipping()
    {
        return $this->addresses()
            ->where('type', 'shipping');
    }

    public function getShippingAttribute()
    {
        return $this->shipping()->first();
    }

    public function getBillingAttribute()
    {
        // Return default=1 if exists, otherwise first address
        return $this->addresses->firstWhere('default', 1) ?? $this->addresses->first();
    }

    public function orders(): Relations\HasMany
    {
        return $this->hasMany(Order::class, 'orderable_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quote::class, 'quotable_id');
    }

    public function getIsDeletableAttribute(): bool
    {
        return $this->orders()->count() === 0
            && $this->quotations()->count() === 0;
    }

    public function scopeOrderByName(Builder $query, $direction = 'asc'): Builder
    {
        return $query->orderBy('first_name', $direction)
            ->orderBy('last_name', $direction);
    }
}
