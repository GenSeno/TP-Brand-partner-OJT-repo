<?php

namespace App\Observers;

use App\Models\ProductOptionValue;

class ProductOptionValueObserver
{
    /**
     * Handle the ProductOptionValue "deleted" event.
     */
    public function deleted(ProductOptionValue $value): void
    {
        if ($value->has('variants')) {
            $value->variants()->delete();
            $value->delete();
        } else {
            $value->forceDelete();
        }
    }
}
