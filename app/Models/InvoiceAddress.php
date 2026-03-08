<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $invoice_id
 * @property ?int $country_id
 * @property ?string $title
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
 * @property ?array $meta
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class InvoiceAddress extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => AddressType::class,
        'meta' => AsArrayObject::class,
    ];

    protected $appends = [
        'full_name',
    ];

    public function invoice(): Relations\BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function country(): Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function fullName(): Attribute
    {
        return Attribute::get(
            fn() => trim("{$this->title} {$this->first_name} {$this->last_name}")
        );
    }
}
