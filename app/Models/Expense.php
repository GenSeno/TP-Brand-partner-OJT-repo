<?php

namespace App\Models;
use App\Lunar\Casts\Price as PriceObject;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Lunar\DataTypes\Price;
use Illuminate\Support\Carbon;
use App\Enums\ExpenseStatus;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Expense extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'total_amount' => PriceObject::class,
        'sub_total' => PriceObject::class,
        'discount_total' => PriceObject::class,
        'discount_breakdown' => 'array'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines()
    {
        return $this->hasMany(ExpenseLine::class);
    }

    public function calculateTotal(): float
    {
        return $this->lines()->sum('amount');
    }

    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where('reference', 'like', "%{$value}%")
            ->orWhereHas('supplier', function ($q) use ($value) {
                $q->where('name', 'like', "%{$value}%");
            });
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->lines()->sum('total');
        $discountBreakdown = collect($this->discount_breakdown ?? [])
            ->map(function ($row) use ($subtotal) {

                if (($row['method'] ?? null) === 'percentage') {
                    $amount = $subtotal * ($row['value'] / 100);
                    $row['amount'] = $amount;
                    $price = new Price((int) $amount, $this->currency);
                    $row['format'] = $price->formatted;
                    $row['decimal'] = $price->decimal;

                }
                return $row;
            })
            ->values();

        $discountTotal = $discountBreakdown->sum('amount');

        $this->sub_total = $subtotal;

        $this->discount_breakdown = $discountBreakdown;
        $this->discount_total = $discountTotal;

        $this->total_amount = max(
            $subtotal - $discountTotal,
            0
        );

        $this->save();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    protected static function booted()
    {
        static::updating(function ($expense) {

            if (!$expense->isDirty('status')) {
                return;
            }

            $now = Carbon::now();

            match ($expense->status) {
                'upcoming' => $expense->upcoming_at = $now,
                'draft' => $expense->draft_at = $now,
                'cancelled' => $expense->cancelled_at = $now,
                default => null,
            };
        });
    }

    public function scopeStatus(Builder $query, ExpenseStatus|string $status): Builder
    {
        $status = $status instanceof ExpenseStatus ? $status : ExpenseStatus::from($status);
        return $query->whereStatus($status);
    }


    public function registerMediaCollections(): void
    {
        $allowed_mime_types = config('file-attachments.allowed_mime_types.default');

        $this->addMediaCollection('expense')
            ->acceptsFile(fn($file) =>
                str_starts_with($file->mimeType, 'image/') || in_array($file->mimeType, $allowed_mime_types))
            ->useDisk('public'); // Use the public disk
    }

    public static function getDefaultLogExcept(): array
    {
        return [
            'reference',
            'status',
        ];
    }
}
