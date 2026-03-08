<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use App\Lunar\Casts;
use App\Lunar\DataTypes\Price;
use App\Lunar\Traits\LogsActivity;
use App\Lunar\ValueObjects\Cart\TaxBreakdown;
use App\Lunar\ValueObjects\Cart\TaxBreakdownAmount;
use App\Observers\PurchaseOrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $supplier_id
 * @property string $reference
 * @property PurchaseOrderStatus $status
 * @property int $sub_total
 * @property array $discount_breakdown
 * @property int $discount_total
 * @property array|TaxBreakdown $tax_breakdown
 * @property int $tax_total
 * @property int $total
 * @property ?string $notes
 * @property string $currency_code
 * @property ?string $compare_currency_code
 * @property float $exchange_rate
 * @property ?\Illuminate\Support\Carbon $order_date
 * @property ?\Illuminate\Support\Carbon $expected_delivery_date
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
#[ObservedBy([PurchaseOrderObserver::class])]
class PurchaseOrder extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'order_date' => 'datetime',
            'expected_delivery_date' => 'datetime',
            'status' => PurchaseOrderStatus::class,
            'sub_total' => Casts\Price::class,
            'discount_breakdown' => Casts\DiscountBreakdown::class,
            'discount_total' => Casts\Price::class,
            'tax_breakdown' => Casts\TaxBreakdown::class,
            'tax_total' => Casts\Price::class,
            'total' => Casts\Price::class,
            'late_payment_charges' => Casts\Price::class,
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function calculateTotals(): void
    {
        $subTotal = (int) $this->lines()->sum('total');

        $this->sub_total = $subTotal;
        $this->discount_total = 0;
        $this->discount_breakdown = [];

        $taxTotal = (int) round($subTotal * 0.12);
        $this->tax_total = $taxTotal;

        $taxBreakdown = new TaxBreakdown();
        $taxBreakdown->addAmount(new TaxBreakdownAmount(
            new Price($taxTotal, Currency::getDefault()),
            'vat_12',
            '12% VAT',
            0.12,
        ));
        $this->tax_breakdown = $taxBreakdown;

        $this->total = $subTotal;

        $this->save();
    }

    public function scopeSearch($query, $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', "%{$term}%"));
        });
    }
}
