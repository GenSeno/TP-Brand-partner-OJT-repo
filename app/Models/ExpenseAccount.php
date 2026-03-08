<?php

namespace App\Models;
use App\Models\Concerns\HasOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;   

class ExpenseAccount extends Model
{
    use HasOptions;
    use HasFactory;

    protected $guarded = [];


    protected $casts = [
        'enabled' => 'boolean'
    ];
    
    protected $appends = ['is_deletable'];
    /**
     * Relationships
     */
     public function lines()
    {
        return $this->hasMany(ExpenseLine::class);
    }
    /**
     * Scopes
     */
    public function scopeOrderByName($query)
    {
        return $query->orderBy('name');
    }

    public function scopeActive($query)
    {
        return $query->where('enabled', 1);
    }

      public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where('name', 'like', "%{$value}%");
    }

       public function getIsDeletableAttribute(): bool
    {
        return $this->lines()->count() === 0;
           
    }
}
