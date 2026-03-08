<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $addressable_type
 * @property int $addressable_id
 * @property AddressType $type
 * @property ?string $title
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property ?string $company_name
 * @property string $line1
 * @property ?string $line2
 * @property ?string $barangay
 * @property string $city
 * @property ?string $province
 * @property ?string $postcode
 * @property int $country_id
 * @property ?string $delivery_instructions
 * @property ?string $email
 * @property ?string $phone
 * @property array $meta
 * @property bool $default
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Address extends Model
{
    use SoftDeletes;
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'default' => 'boolean',
        'meta' => AsArrayObject::class,
        'type' => AddressType::class,
    ];

    public function country(): Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function owner(): Relations\MorphTo
    {
        return $this->morphTo('addressable');
    }

    public function getFormattedAddressAttribute(): string
    {
        $addressParts = [];

        if ($this->line1) {
            $addressParts[] = $this->line1;
        }

        if ($this->line2) {
            $addressParts[] = $this->line2;
        }

        if ($this->barangay) {
            $addressParts[] = $this->barangay;
        }

        if ($this->city) {
            $addressParts[] = $this->city;
        }

        if ($this->province) {
            $addressParts[] = $this->province;
        }

        if ($this->postcode) {
            $addressParts[] = $this->postcode;
        }

        if ($this->country && $this->country->name) {
            $addressParts[] = $this->country->name;
        }

        return implode(', ', $addressParts);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn(): string => "{$this->title} {$this->first_name} {$this->last_name}",
        );
    }

    public function scopeBilling(Builder $query): Builder
    {
        return $query->where('type', AddressType::BILLING);
    }

    public function scopeShipping(Builder $query): Builder
    {
        return $query->where('type', AddressType::SHIPPING);
    }

    public function scopeIsDefault(Builder $query, bool $default = true): Builder
    {
        return $query->where('default', $default);
    }
}
