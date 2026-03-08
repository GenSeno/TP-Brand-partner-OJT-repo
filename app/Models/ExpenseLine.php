<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lunar\Casts\Price as PriceObject;

class ExpenseLine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price' => PriceObject::class,
        'total' => PriceObject::class,
    ];


    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
    
      public function expenseAccount()
    {
        return $this->belongsTo(ExpenseAccount::class);
    }
}
