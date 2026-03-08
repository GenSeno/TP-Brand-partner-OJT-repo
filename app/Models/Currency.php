<?php

namespace App\Models;

use App\Lunar\Traits\LogsActivity;
use App\Models\Concerns\HasOptions;
use App\Traits\HasDefaultRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property string $code
 * @property ?string $symbol
 * @property string $name
 * @property float $exchange_rate
 * @property int $decimal_places
 * @property bool $enabled
 * @property bool $default
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Currency extends Model
{
    use HasOptions;
    use HasDefaultRecord;
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'default' => 'boolean',
    ];

    public function scopeEnabled(Builder $query, $enabled = true): Builder
    {
        return $query->whereEnabled($enabled);
    }

    public function prices(): Relations\HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function factor(): Attribute
    {
        /**
         * If we figure out how many decimal places we need, we can work
         * out what the initial divided value should be to get the cents.
         *
         * E.g. For two decimal places, we need to divide by 100.
         */
        return Attribute::get(
            function () {
                if ($this->decimal_places < 1) {
                    return 1;
                }

                return sprintf("1%0{$this->decimal_places}d", 0);
            }
        );
    }

    public static function defaultId()
    {
        return self::where('default', 1)->value('id') ?? 1;
    }
}
