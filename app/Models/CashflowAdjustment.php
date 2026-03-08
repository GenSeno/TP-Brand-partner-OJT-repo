<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CashflowAdjustment extends Model
{
    use HasFactory;

    protected $table = 'cashflow_adjustments';

    protected $fillable = [
        'method',        
        'group',        
        'amount',
        'description',
        'posted_by',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(Staff::class, 'posted_by');
    }

    // Accessors
    public function getIsCashInAttribute(): bool
    {
        return $this->method === 'in';
    }

    public function getIsCashOutAttribute(): bool
    {
        return $this->method === 'out';
    }

    public function getSignedAmountAttribute(): float
    {
        return $this->is_cash_out ? -1 * $this->amount : $this->amount;
    }

    // Scopes
    public function scopeCashIn($query)
    {
        return $query->where('method', 'in');
    }

    public function scopeCashOut($query)
    {
        return $query->where('method', 'out');
    }
}
