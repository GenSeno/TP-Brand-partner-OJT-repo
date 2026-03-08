<?php

namespace App\Models;

use App\States\JobOrderState\JobOrderState;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_line_id
 * @property int $staff_id
 * @property JobOrderState $state
 * @property int $quantity
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class JobOrderAssignment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'state' => JobOrderState::class,
        'meta' => AsArrayObject::class,
    ];

    public function orderLine()
    {
        return $this->belongsTo(OrderLine::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
