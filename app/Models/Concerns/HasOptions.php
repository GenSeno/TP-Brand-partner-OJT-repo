<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasOptions
{
    public function scopeGetOptions(Builder $query, $label, $value = 'id'): Collection
    {
        return $query->get()
            ->map(fn($item) => [
                'label' => $item->{$label},
                'value' => $item->{$value},
            ]);
    }
}
