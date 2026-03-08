<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Lunar\Casts\Price;
use App\Lunar\Traits\LogsActivity;
use App\Observers\PaymentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\PaymentMethod;

/**
 * @property int $id
 * @property int $invoice_id
 * @property int $amount
 * @property string $method
 * @property ?string $reference
 * @property ?string $internal_reference
 * @property ?\Illuminate\Support\Carbon $paid_at
 * @property int $created_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
#[ObservedBy(PaymentObserver::class)]
class Payment extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'amount' => Price::class,
        'paid_at' => 'datetime',
    ];

    public function invoice(): Relations\BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): Relations\BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function order(): Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Order::class,
            Invoice::class,
            'id',
            'id',
            'invoice_id',
            'order_id'
        );
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('invoice', function ($q2) use ($search) {
                $q2->where('reference', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            })
            ->orWhere('internal_reference', 'like', "%{$search}%")
            ->orWhere('method', 'like', "%{$search}%");
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

    public function checkOrderInvoice()
    {
        $this->load('invoice.order');

        $invoice = $this->invoice;
        $order = $invoice->order;

        $paid = $invoice->payments()->sum('amount');
        if ($paid >= $invoice->amount_due->value) {
            $invoice->updateQuietly(['status' => InvoiceStatus::PAID]);

            $totalPaid = $order->invoices()->status(InvoiceStatus::PAID)->sum('amount_due');
            if ($totalPaid >= $order->total->value) {
                $order->updateQuietly(['status' => OrderStatus::PAID]);
            }
        } elseif ($paid > 0) {
            $invoice->updateQuietly(['status' => InvoiceStatus::PARTIALLY_PAID]);

            if ($order->status->is(OrderStatus::UNPAID)) {
                $order->updateQuietly(['status' => OrderStatus::PARTIALLY_PAID]);
            }
        } else {
            $invoice->updateQuietly(['status' => InvoiceStatus::UNPAID]);

            if (!$order->status->in(OrderStatus::UNPAID, OrderStatus::CANCELLED)) {
                $order->updateQuietly(['status' => OrderStatus::UNPAID]);
            }
        }
    }

    
}
