<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        ProductVariant::createByProduct($product);
    }
}
