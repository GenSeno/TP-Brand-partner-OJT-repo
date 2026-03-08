<?php

namespace App\Actions;

use App\Models\ProductOptionValue;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateVariantSKU
{
    use AsAction;

    public function handle($id, $values)
    {
        if ($values instanceof Collection) {
            // Check if collection contains models or IDs
            $firstItem = $values->first();
            if ($firstItem instanceof ProductOptionValue) {
                $values = $values->sortBy('id');
            } else {
                $values = ProductOptionValue::query()
                    ->whereIn('id', $values->toArray())
                    ->orderBy('id')
                    ->get();
            }
        } elseif (is_array($values)) {
            // Check if array contains models or IDs
            $firstItem = reset($values);
            if ($firstItem instanceof ProductOptionValue) {
                $values = collect($values)->sortBy('id');
            } else {
                $values = ProductOptionValue::query()
                    ->whereIn('id', $values)
                    ->orderBy('id')
                    ->get();
            }
        } else {
            throw new \InvalidArgumentException('$values must be a Collection or array of ProductOptionValue models or IDs');
        }

        $parts = [$id];
        foreach ($values as $value) {
            $parts[] = strtoupper(
                substr($value->value, 0, 3)
            );
        }

        return implode('-', $parts);
    }
}
