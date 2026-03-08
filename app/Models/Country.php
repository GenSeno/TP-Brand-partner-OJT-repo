<?php

namespace App\Models;

use App\Models\Concerns\HasOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property string $name
 * @property string $iso3
 * @property ?string $iso2
 * @property string $phonecode
 * @property ?string $capital
 * @property string $currency
 * @property ?string $native
 * @property string $emoji
 * @property string $emojiU
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Country extends Model
{
    use HasOptions;

    protected $fillable = [
        'name',
        'iso3',
        'iso2',
        'phonecode',
        'capital',
        'currency',
        'native'
    ];

    public function states(): Relations\HasMany
    {
        return $this->hasMany(State::class);
    }

    public function scopeForDropdown($query)
    {
        return $query->select(['id', 'name', 'iso2', 'emoji'])
            ->orderBy('name');
    }
}
