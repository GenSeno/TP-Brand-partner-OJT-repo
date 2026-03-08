<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Enums\PersonTitle;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property ?int $country_id
 * @property ?PersonTitle $title
 * @property ?string $first_name
 * @property ?string $last_name
 * @property ?string $company_name
 * @property ?string $line1
 * @property ?string $line2
 * @property ?string $barangay
 * @property ?string $city
 * @property ?string $province
 * @property ?string $postcode
 * @property ?string $delivery_instructions
 * @property ?string $email
 * @property ?string $phone
 * @property AddressType $type
 * @property ?string $shipping_option
 * @property array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class OrderAddress extends Model
{
    /** @use HasFactory<\Database\Factories\OrderAddressFactory> */
    use HasFactory;
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'title' => PersonTitle::class,
        'meta' => AsArrayObject::class,
        'type' => AddressType::class,
    ];

    protected $appends = [
        'full_name',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function fullName(): Attribute
    {
        return Attribute::get(
            fn() => trim(($this->title->value ?? '') . ' ' . ($this->first_name ?? '') . ' ' . ($this->last_name ?? ''))
        );
    }
}
