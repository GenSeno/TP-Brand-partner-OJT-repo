<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use App\Enums\QuoteType;
use App\Lunar\Casts;
use App\Lunar\DataTypes;
use App\Lunar\DataTypes\Price;
use App\Lunar\Traits\LogsActivity;
use App\Lunar\ValueObjects;
use App\Lunar\ValueObjects\Cart\DiscountBreakdown;
use App\Lunar\ValueObjects\Cart\DiscountBreakdownLine;
use App\Lunar\ValueObjects\Cart\TaxBreakdown;
use App\Lunar\ValueObjects\Cart\TaxBreakdownAmount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property ?string $quotable_type
 * @property ?int $quotable_id
 * @property ?int $merged_id
 * @property int $currency_id
 * @property ?int $order_id
 * @property ?string $reference
 * @property ?string $description
 * @property QuoteType $type
 * @property QuoteStatus $status
 * @property DataTypes\Price $sub_total
 * @property DataTypes\Price $discount_total
 * @property array $discount_breakdown
 * @property ValueObjects\Cart\ShippingBreakdown $shipping_breakdown
 * @property DataTypes\Price $shipping_total
 * @property ValueObjects\Cart\TaxBreakdown $tax_breakdown
 * @property DataTypes\Price $tax_total
 * @property DataTypes\Price $total
 * @property ?string $payment_terms
 * @property ?string $general_terms
 * @property DataTypes\Price $late_payment_charges
 * @property int $estimated_lead_time
 * @property ?\Illuminate\Support\Carbon $quoted_at
 * @property ?\Illuminate\Support\Carbon $expired_at
 * @property ?\Illuminate\Support\Carbon $completed_at
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class Quote extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'quoted_at' => 'datetime',
            'expired_at' => 'datetime',
            'completed_at' => 'datetime',
            'sub_total' => Casts\Price::class,
            'discount_breakdown' => 'array',
            'discount_total' => Casts\Price::class,
            'shipping_breakdown' => Casts\ShippingBreakdown::class,
            'shipping_total' => Casts\Price::class,
            'tax_breakdown' => Casts\TaxBreakdown::class,
            'tax_total' => Casts\Price::class,
            'total' => Casts\Price::class,
            'late_payment_charges' => Casts\Price::class,
            'status' => QuoteStatus::class,
            'type' => QuoteType::class,
            'expected_delivery' => 'datetime',
        ];
    }

    public function lines(): Relations\HasMany
    {
        return $this->hasMany(QuoteLine::class);
    }

    public function physicalLines(): Relations\HasMany
    {
        return $this->lines()->where('type', 'physical');
    }

    public function digitalLines(): Relations\HasMany
    {
        return $this->lines()->where('type', 'digital');
    }

    public function shippingLines(): Relations\HasMany
    {
        return $this->lines()->where('type', 'shipping');
    }

    public function productLines(): Relations\HasMany
    {
        return $this->lines()->where('type', '!=', 'shipping');
    }

    public function currency(): Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function owner(): Relations\MorphTo
    {
        return $this->morphTo('quotable');
    }

    public function scopeUnmerged(Builder $query): Builder
    {
        return $query->whereNull('merged_id');
    }

    public function addresses(): Relations\HasMany
    {
        return $this->hasMany(QuoteAddress::class);
    }

    public function shippingAddress(): Relations\HasOne
    {
        return $this->hasOne(QuoteAddress::class)->where('type', 'shipping');
    }

    public function billingAddress(): Relations\HasOne
    {
        return $this->hasOne(QuoteAddress::class)->where('type', 'billing');
    }

    public function order(): Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function images()
    {
        return $this->media()->where('collection_name', 'images');
    }

    public function scopeStatus(Builder $query, QuoteStatus|string $status): Builder
    {
        $status = $status instanceof QuoteStatus ? $status : QuoteStatus::from($status);

        return $query->whereStatus($status);
    }

    public function calculate(bool $force = false)
    {
        if (! $force && $this->isCalculated()) {
            // Don't recalculate
            return $this;
        }

        $discountTotal = $this->lines->sum('discount_total.value');
        $subTotal = $this->lines->sum('sub_total.value');
        $total = $this->lines->sum('total.value') + $this->shippingTotal?->value;

        $this->sub_total = new Price($subTotal, $this->currency, 1);
        $this->discount_total = new Price($discountTotal, $this->currency, 1);
        $this->total = new Price($total, $this->currency, 1);

    }

    public function recalculate()
    {
        return $this->calculate(force: true);
    }

    public function isCalculated()
    {
        return ! blank($this->total) && $this->lines->every(
            fn (QuoteLine $line) => ! blank($line->total)
        );
    }

    public function createOrder()
    {
        //
    }

    public function quotes()
    {
        return $this->morphMany(Quote::class, 'quotable');
    }

    public function scopeSearch($query, $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                ->orWhereHas('billingAddress', function ($q2) use ($term) {
                    $q2->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('company_name', 'like', "%{$term}%");
                });
        });
    }

    public function recalculateTotals(): void
    {
        $currency = $this->currency;

        $subtotal = $this->lines->sum(
            fn ($line) => $line->quantity * $line->purchase_price->value
        );

        $shippingTotal = $this->shipping_breakdown?->items->sum(fn ($i) => $i->price->value);

        $updatedTaxAmounts = $this->tax_breakdown->amounts->map(function (TaxBreakdownAmount $item) use ($subtotal, $currency) {

            if ($item->percentage > 0) {
                $value = (int) ($subtotal * ($item->percentage / 100));

                $price = new Price($value, $currency);

                $item->price = $price;
                $item->identifier = $item->identifier;
                $item->description = $price->formatted;
            }

            return $item;
        });

        $taxBreakdown = new TaxBreakdown($updatedTaxAmounts);
        $taxTotal = $updatedTaxAmounts->sum(fn ($item) => $item->price->value);

        $discountBreakdownArr = $this->discount_breakdown['amounts'] ?? [];
        $updatedDiscountLines = collect($discountBreakdownArr)->map(function ($line) use ($subtotal, $currency) {

            $id = $line['identifier'];
            $desc = $line['description'];

            if (($line['type'] ?? 'fixed') === 'percentage') {

                return DiscountBreakdownLine::percentage(
                    $subtotal,
                    (float) $line['percentage'],
                    $currency,
                    $id,
                    $desc
                );
            }

            return DiscountBreakdownLine::fixed(
                (int) $line['price']['decimal'],
                $currency,
                $id,
                $desc
            );
        });

        $discountBreakdown = new DiscountBreakdown($updatedDiscountLines);
        $discountTotal = $updatedDiscountLines->sum(fn ($line) => $line->price->value);

        $total = $subtotal - $discountTotal + $shippingTotal + $taxTotal;

        $this->update([
            'sub_total' => $subtotal,
            'tax_breakdown' => $taxBreakdown,
            'tax_total' => $taxTotal,
            'discount_breakdown' => $discountBreakdown,
            'discount_total' => $discountTotal,
            'total' => $total,
        ]);
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
        static::creating(function (Quote $quote) {
            if ($quote->reference && ! $quote->share_token_hash) {
                $quote->share_token_hash = substr(
                    hash_hmac('sha256', $quote->reference, config('app.key')),
                    0,
                    8
                );
            }
        });

        static::updating(function (Quote $quote) {
            if ($quote->isDirty('reference') && $quote->reference) {
                $quote->share_token_hash = substr(
                    hash_hmac('sha256', $quote->reference, config('app.key')),
                    0,
                    8
                );
            }
        });
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->lines->sum('quantity');
    }

    public function registerMediaCollections(): void
    {
        $allowed_mime_types = config('file-attachments.allowed_mime_types.default');

        $this->addMediaCollection('files')
            ->acceptsFile(fn ($file) => str_starts_with($file->mimeType, 'image/') || in_array($file->mimeType, $allowed_mime_types))
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
