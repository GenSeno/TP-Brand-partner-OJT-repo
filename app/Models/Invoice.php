<?php

namespace App\Models;

use App\Enums\InvoiceLineType;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Lunar\Casts\Price;
use App\Lunar\DataTypes\Price as PriceDataType;
use App\Lunar\Traits\LogsActivity;
use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Lunar\Casts\ShippingBreakdown;

/**
 * @property int $id
 * @property ?int $order_id
 * @property ?string $reference
 * @property ?string $description
 * @property ?InvoiceType $type
 * @property ?\Illuminate\Support\Carbon $invoiced_at
 * @property ?\Illuminate\Support\Carbon $due_at
 * @property string $customer_name
 * @property ?string $customer_email
 * @property PriceDataType $sub_total
 * @property PriceDataType $total
 * @property PriceDataType $amount_due
 * @property PriceDataType $vatable_amount
 * @property PriceDataType $vat_amount
 * @property InvoiceStatus $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy(InvoiceObserver::class)]
class Invoice extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    protected $guarded = [];

    protected $appends = ['type_label','summary','has_payments','current_sequence'];

    protected $casts = [
        'invoiced_at' => 'datetime',
        'due_at' => 'datetime',
        'sub_total' => Price::class,
        'total' => Price::class,
        'amount_due' => Price::class,
        'vatable_amount' => Price::class,
        'vat_amount' => Price::class,
        'status' => InvoiceStatus::class,
        'type' => InvoiceType::class,
        'shipping_breakdown' => ShippingBreakdown::class,
        'shipping_total' => Price::class,
    ];

    public function typeLabel(): Attribute
    {
        return Attribute::get(fn () => $this->type?->getLabel());
    }

    public function order(): Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function lines(): Relations\HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function productLines(): Relations\HasMany
    {
        return $this->lines()->where('type', InvoiceLineType::PRODUCT);
    }

    public function shippingLines(): Relations\HasMany
    {
        return $this->lines()->where('type', InvoiceLineType::SHIPPING);
    }

    public function addresses(): Relations\HasMany
    {
        return $this->hasMany(InvoiceAddress::class);
    }

    public function billingAddress(): Relations\HasOne
    {
        return $this->hasOne(InvoiceAddress::class)->whereType('billing');
    }

    public function shippingAddress(): Relations\HasOne
    {
        return $this->hasOne(InvoiceAddress::class)->whereType('shipping');
    }

    public function payments(): Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function quotation(): Relations\HasOneThrough
    {
        return $this->hasOneThrough(Quote::class, Order::class, 'id', 'order_id', 'order_id', 'id');
    }

    public function jobOrder(): Relations\HasOneThrough
    {
        return $this->hasOneThrough(JobOrder::class, Order::class, 'id', 'order_id', 'order_id', 'id');
    }

    public function summary(): Attribute
    {
        return Attribute::get(function () {
            $paid = $this->payments()->sum('amount');
            $balance = max(0, $this->amount_due->value - $paid);
            $with_shipping = max(0, $this->amount_due->value + $this->shipping_total->value);

            return [
                'amount_paid' => new PriceDataType((int) $paid, $this->currency_code),
                'amount_balance' => new PriceDataType((int) $balance, $this->currency_code),
                'with_shipping' => new PriceDataType((int) $with_shipping, $this->currency_code),
            ];
        });
    }

    public function scopeStatus(Builder $query, InvoiceStatus|string ...$status): Builder
    {
        return $query->whereIn('status', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('reference', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_email', 'like', "%{$search}%")
                ->orWhereHas('order', function ($q2) use ($search) {
                    $q2->where('reference', 'like', "%{$search}%");
                });
        });
    }

    public function getShareTokenAttribute(): string
    {
        return $this->share_token_hash ?? substr(
            hash_hmac('sha256', $this->reference, config('app.key')),
            0,
            8
        );
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if ($invoice->reference && ! $invoice->share_token_hash) {
                $invoice->share_token_hash = substr(
                    hash_hmac('sha256', $invoice->reference, config('app.key')),
                    0,
                    8
                );
            }
        });

        static::updating(function (Invoice $invoice) {
            if ($invoice->isDirty('reference') && $invoice->reference) {
                $invoice->share_token_hash = substr(
                    hash_hmac('sha256', $invoice->reference, config('app.key')),
                    0,
                    8
                );
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $allowed_mime_types = config('file-attachments.allowed_mime_types.default');

        $this->addMediaCollection('files')
            ->acceptsFile(fn($file) =>
                str_starts_with($file->mimeType, 'image/') || in_array($file->mimeType, $allowed_mime_types))
            ->useDisk('private');
    }

    public static function getDefaultLogExcept(): array
    {
        return [
            'reference',
            'status',
        ];
    }

    public function hasPayments(): Attribute
    {
        return Attribute::get(
             fn () => $this->payments()->count()
        );
    }

    public function currentSequence(): Attribute
    {
        return Attribute::get(function () {

            if (! $this->order_id) {
                return null;
            }

            // Only consider DOWN_PAYMENT, INSTALLMENT, FINAL_PAYMENT
            $invoices = self::where('order_id', $this->order_id)
                ->whereIn('type', [
                    InvoiceType::DOWN_PAYMENT,
                    InvoiceType::INSTALLMENT,
                    InvoiceType::FINAL_PAYMENT
                ])
                ->orderBy('invoiced_at', 'asc')
                ->pluck('id'); // only IDs

            // Find the position of this invoice
            $sequence = $invoices->search($this->id);

            if ($sequence === false) {
                return null;
            }

            return $sequence + 1; // make it 1-based
        });
    }

    public function billingUpToCurrent()
    {
        if (! $this->order_id) {
            return collect();
        }

        // Fetch all DOWN_PAYMENT / INSTALLMENT / FINAL_PAYMENT for the same order, ordered by invoiced_at
        $invoices = self::where('order_id', $this->order_id)
            ->whereIn('type', [
                InvoiceType::DOWN_PAYMENT,
                InvoiceType::INSTALLMENT,
                InvoiceType::FINAL_PAYMENT
            ])
            ->orderBy('invoiced_at', 'asc')
            ->get();

        $result = collect();

        foreach ($invoices as $inv) {
            $result->push($inv);
            if ($inv->id === $this->id) {
                break; // Stop at current invoice
            }
        }

        return $result;
    }

     public function totalBilledUpToCurrent(): PriceDataType
    {
        $total = $this->billingUpToCurrent()->sum(function ($inv) {
            return $inv->payments()->sum('amount');
        });

        return new PriceDataType((int) $total, $this->currency_code);
    }

    // Unbilled amount up to current invoice
    public function unbilledAmountUpToCurrent(): PriceDataType
    {
        $total = $this->billingUpToCurrent()->sum(function ($inv) {
            return $inv->payments()->sum('amount');
        });

        $balance = $this->total->value - $total;
        return new PriceDataType((int) $balance, $this->currency_code);
    }
}
