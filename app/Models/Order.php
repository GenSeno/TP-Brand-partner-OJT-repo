<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\JobOrderUrgency;
use App\Enums\OrderStatus;
use App\Lunar\Casts\DiscountBreakdown;
use App\Lunar\Casts\Price;
use App\Lunar\DataTypes;
use App\Lunar\DataTypes\Price as PriceDataType;
use App\Lunar\Traits\LogsActivity;
use App\Lunar\Casts\ShippingBreakdown;
use App\Lunar\Casts\TaxBreakdown;
use App\Lunar\ValueObjects;
use App\Lunar\ValueObjects\Cart\TaxBreakdownAmount;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use App\Models\JobOrder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property ?string $orderable_type
 * @property ?int $orderable_id
 * @property bool $new_customer
 * @property OrderStatus $status
 * @property ?string $reference
 * @property DataTypes\Price $sub_total
 * @property DataTypes\Price $discount_total
 * @property ?ValueObjects\Cart\DiscountBreakdown $discount_breakdown
 * @property ?ValueObjects\Cart\ShippingBreakdown $shipping_breakdown
 * @property DataTypes\Price $shipping_total
 * @property ?ValueObjects\Cart\TaxBreakdown $tax_breakdown
 * @property DataTypes\Price $tax_total
 * @property DataTypes\Price $total
 * @property ?string $notes
 * @property string $currency_code
 * @property ?string $compare_currency_code
 * @property float $exchange_rate
 * @property ?\Illuminate\Support\Carbon $placed_at
 * @property ?\Illuminate\Support\Carbon $expected_delivery
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy(OrderObserver::class)]
class Order extends Model implements Contract\OrderPrintable, HasMedia
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;

    protected $casts = [
        'status' => OrderStatus::class,
        'tax_breakdown' => TaxBreakdown::class,
        'meta' => AsArrayObject::class,
        'placed_at' => 'datetime',
        'expected_delivery' => 'datetime',
        'sub_total' => Price::class,
        'discount_total' => Price::class,
        'discount_breakdown' => DiscountBreakdown::class,
        'shipping_breakdown' => ShippingBreakdown::class,
        'tax_total' => Price::class,
        'total' => Price::class,
        'shipping_total' => Price::class,
        'new_customer' => 'boolean',
    ];

    protected $guarded = [];

    protected $appends = ['billing_summary'];

    public function lines(): Relations\HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function physicalLines(): Relations\HasMany
    {
        return $this->lines()->whereType('physical');
    }

    public function digitalLines(): Relations\HasMany
    {
        return $this->lines()->whereType('digital');
    }

    public function shippingLines(): Relations\HasMany
    {
        return $this->lines()->whereType('shipping');
    }

    public function productLines(): Relations\HasMany
    {
        return $this->lines()->where('type', '!=', 'shipping');
    }

    public function currency(): Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function addresses(): Relations\HasMany
    {
        return $this->hasMany(OrderAddress::class, 'order_id');
    }

    public function shippingAddress(): Relations\HasOne
    {
        return $this->hasOne(OrderAddress::class, 'order_id')->whereType('shipping');
    }

    public function billingAddress(): Relations\HasOne
    {
        return $this->hasOne(OrderAddress::class, 'order_id')->whereType('billing');
    }

    public function orderable(): Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function jobOrder(): Relations\HasOne
    {
        return $this->hasOne(JobOrder::class);
    }

    public function printLines(): Relations\MorphMany
    {
        return $this->morphMany(OrderPrintLine::class, 'printable');
    }

    public function invoices(): Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotation(): Relations\HasOne
    {
        return $this->hasOne(Quote::class);
    }

    public function downPayment(): Relations\HasOne
    {
        return $this->hasOne(Invoice::class)->whereType(InvoiceType::DOWN_PAYMENT);
    }

    public function installments(): Relations\HasMany
    {
        return $this->hasMany(Invoice::class)->whereType(InvoiceType::INSTALLMENT);
    }

    public function finalPayment(): Relations\HasOne
    {
        return $this->hasOne(Invoice::class)->whereType(InvoiceType::FINAL_PAYMENT);
    }

    public function billedInvoices(): Relations\HasMany
    {
        return $this->hasMany(Invoice::class)->whereIn('status', [
            InvoiceStatus::PAID,
            InvoiceStatus::PARTIALLY_PAID,
        ]);
    }

    public function billingSummary(): Attribute
    {
        return Attribute::get(function () {
            $currency = $this->currency;
            $total = (int) $this->total->value;
            $billed = (int) $this->invoices()->status(InvoiceStatus::UNPAID, InvoiceStatus::PAID, InvoiceStatus::PARTIALLY_PAID)->sum('amount_due');
            $unbilled = max(0, $total - $billed);
            $paid = (int) $this->invoices()->whereHas('payments')->withSum('payments', 'amount')->get()->sum('payments_sum_amount');
            $balance = max(0, $total - $paid);

            return [
                'amount_billed' => new PriceDataType($billed, $currency),
                'amount_unbilled' => new PriceDataType($unbilled, $currency),
                'amount_paid' => new PriceDataType($paid, $currency),
                'amount_balance' => new PriceDataType($balance, $currency),
            ];
        });
    }

    public function isDraft(): bool
    {
        return !$this->isPlaced();
    }

    public function isPlaced(): bool
    {
        return !blank($this->placed_at);
    }

    public function scopeSearch($query, $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
                ->orWhereHasMorph('orderable', [Customer::class], function ($q2) use ($term) {
                    $q2->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('company_name', 'like', "%{$term}%");
                });
        });
    }

    public function scopePlaced(Builder $query): Builder
    {
        return $query->whereNotNull('placed_at');
    }

    public function recalculate()
    {
        $subTotal = $this->lines()->sum('sub_total');
        $discountTotal = $this->lines()->sum('discount_total');
        $shippingTotal = $this->shipping_total?->value ?? 0;
        $total = $subTotal - $discountTotal + $shippingTotal;

        // VAT calculation (12%) --vat
        $vatableAmount = $total / 1.12;
        $vat = $vatableAmount * 0.12;

        $taxBreakdown = new ValueObjects\Cart\TaxBreakdown();
        $taxBreakdown->addAmount(new TaxBreakdownAmount(
            price: new PriceDataType((int) $vatableAmount, Currency::getDefault()),
            identifier: 'VATABLE',
            description: 'Vatable Amount',
            percentage: 0.88,
        ));
        $taxBreakdown->addAmount(new TaxBreakdownAmount(
            price: new PriceDataType((int) $vat, Currency::getDefault()),
            identifier: 'VAT',
            description: 'Value Added Tax (12%)',
            percentage: 0.12,
        ));

        $this->update([
            'sub_total' => $subTotal,
            'discount_total' => $discountTotal,
            'tax_breakdown' => $taxBreakdown,
            'tax_total' => (int) $vat,
            'total' => (int) $total,
        ]);
    }

    public function canCreateJobOrder(): bool
    {
        return $this->physicalLines()->exists() && !$this->jobOrder;
    }

    public function canCreateInvoice(): bool
    {
        return $this->isPlaced() && !$this->invoice && !$this->billingAddress;
    }

    /**
     * Create a job order from this order.
     */
    public function createJobOrder(array $attributes = []): JobOrder
    {
        if (!$this->canCreateJobOrder()) {
            throw new \Exception('Cannot create job order from this order.');
        }

        $urgency = $attributes['urgency_flag'] ?? JobOrderUrgency::NORMAL;
        $leadTime = $attributes['lead_time'] ?? 7; // Default 7 days
        $dueAt = $attributes['due_at'] ?? now()->addDays($leadTime);
        $estimatedDelivery = $attributes['estimated_delivery'] ?? now()->addDays($leadTime + 1);

        $jobOrder = $this->jobOrder()->create(array_merge([
            'urgency_flag' => $urgency,
            'lead_time' => $leadTime,
            'ordered_at' => $this->isPlaced() ? $this->placed_at : now(),
            'due_at' => $dueAt,
            'estimated_delivery' => $estimatedDelivery,
        ], $attributes));

        // Collect unique products from order lines
        $productIds = [];
        foreach ($this->physicalLines as $line) {
            if ($line->purchasable && $line->purchasable instanceof ProductVariant) {
                $productId = $line->purchasable->product->id;
                if (!in_array($productId, $productIds)) {
                    $productIds[] = $productId;
                }
            }
        }

        // Create job order products (quantities are tracked through order lines)
        foreach ($productIds as $productId) {
            $jobOrder->products()->create([
                'product_id' => $productId,
            ]);
        }

        return $jobOrder;
    }

    public function canBeCancelled(): bool
    {
        return !$this->status->is(OrderStatus::CANCELLED)
            && $this->jobOrder?->canBeCancelled();
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
}
