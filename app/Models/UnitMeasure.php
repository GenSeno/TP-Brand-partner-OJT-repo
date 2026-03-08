<?php

namespace App\Models;

use App\Enums\UomType;
use App\Lunar\Traits\LogsActivity;
use App\Models\Concerns\HasOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property UomType $type
 * @property bool $enabled
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class UnitMeasure extends Model
{
    use HasOptions;
    use LogsActivity;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'type' => UomType::class,
        ];
    }

    public function scopeType(Builder $query, UomType|string $type): Builder
    {
        if ($type instanceof UomType) {
            $type = $type->value;
        }
        return $query->where('type', $type);
    }

    public function scopeEnabled(Builder $query, bool $enabled = true): Builder
    {
        return $query->where('enabled', $enabled);
    }
}
