<?php

namespace App\Models;

use App\Lunar\Casts\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $invoice_id
 * @property int $product_id
 * @property string $product_name
 * @property ?string $sku
 * @property string $uom_code
 * @property int $unit_price
 * @property int $quantity
 * @property int $total
 * @property array $options_payload
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class InvoiceLine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'unit_price' => Price::class,
        'total' => Price::class,
        'options_payload' => 'array',
    ];

    public function invoice(): Relations\BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uom(): Relations\BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'uom_code', 'code');
    }
}
