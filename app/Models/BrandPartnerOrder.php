<?php

namespace App\Models;

use App\Enums\BrandPartnerOrderStatus;
use App\Lunar\Traits\LogsActivity;
use App\Observers\BrandPartnerOrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $brand_partner_id
 * @property ?int $customer_id
 * @property ?int $user_id
 * @property ?string $reference
 * @property string $customer_name
 * @property string $customer_email
 * @property ?string $customer_phone
 * @property BrandPartnerOrderStatus $status
 * @property int $sub_total
 * @property int $tax_total
 * @property int $total
 * @property ?string $notes
 * @property ?\Illuminate\Support\Carbon $placed_at
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy(BrandPartnerOrderObserver::class)]
class BrandPartnerOrder extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'brand_partner_id',
        'customer_id',
        'user_id',
        'reference',
        'customer_name',
        'company_name',
        'customer_email',
        'customer_phone',
        'address',
        'address_line1',
        'address_line2',
        'barangay',
        'city',
        'province',
        'postcode',
        'status',
        'payment_status',
        'sub_total',
        'tax_total',
        'total',
        'notes',
        'jo_number',
        'jo_status',
        'placed_at',
        'meta',
        'payment_invoice_id',
        'payment_status',
    ];

    protected $casts = [
        'status' => BrandPartnerOrderStatus::class,
        'sub_total' => 'integer',
        'tax_total' => 'integer',
        'total' => 'integer',
        'placed_at' => 'datetime',
        'meta' => AsArrayObject::class,
    ];

    protected $appends = [
        'formatted_total',
        'shipping_address',
        'has_pre_order',
        'pre_order_quantity',
        'total_quantity',
    ];

    // Relationships
    public function brandPartner(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartner::class);
    }

    public function customer(): Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerOrderLine::class, 'order_id');
    }

    // Accessors
    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->total / 100, 2),
        );
    }

    protected function shippingAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => collect([
                $this->address_line1,
                $this->address_line2,
                $this->barangay,
                $this->city,
                $this->province,
                $this->postcode,
            ])->filter()->implode(', '),
        );
    }

    protected function hasPreOrder(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('lines')
                ? $this->lines->contains(fn ($line) => $line->meta['pre_order'] ?? false)
                : false,
        );
    }

    protected function preOrderQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('lines')
                ? $this->lines->sum(fn ($line) => ($line->meta['pre_order'] ?? false) ? $line->quantity : 0)
                : 0,
        );
    }

    protected function totalQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('lines')
                ? $this->lines->sum('quantity')
                : 0,
        );
    }

    // Scopes
    public function scopeStatus(Builder $query, BrandPartnerOrderStatus|string $status): Builder
    {
        $status = $status instanceof BrandPartnerOrderStatus ? $status : BrandPartnerOrderStatus::from($status);

        return $query->where('status', $status);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', BrandPartnerOrderStatus::PENDING);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', BrandPartnerOrderStatus::CONFIRMED);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', BrandPartnerOrderStatus::COMPLETED);
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (! trim($value)) {
            return $query;
        }

        return $query->where(function ($q) use ($value) {
            $q->where('reference', 'like', "%{$value}%")
                ->orWhere('customer_name', 'like', "%{$value}%")
                ->orWhere('customer_email', 'like', "%{$value}%");
        });
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === BrandPartnerOrderStatus::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BrandPartnerOrderStatus::CONFIRMED;
    }

    public function isCompleted(): bool
    {
        return $this->status === BrandPartnerOrderStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === BrandPartnerOrderStatus::CANCELLED;
    }

    public function confirm(): bool
    {
        $this->status = BrandPartnerOrderStatus::CONFIRMED;

        return $this->save();
    }

    public function complete(): bool
    {
        $this->status = BrandPartnerOrderStatus::COMPLETED;

        return $this->save();
    }

    public function cancel(): bool
    {
        $this->status = BrandPartnerOrderStatus::CANCELLED;

        return $this->save();
    }

    public function recalculate(): void
    {
        $subTotal = $this->lines->sum('total');
        $this->sub_total = $subTotal;
        $this->total = $subTotal + $this->tax_total;
        $this->save();
    }
}
