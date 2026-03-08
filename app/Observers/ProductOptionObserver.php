<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductVariant;

class ProductOptionObserver
{
    /**
     * Handle the ProductOption "deleted" event.
     */
    public function deleted(ProductOption $productOption): void
    {
        if ($productOption->has('products')) {
            $productOption->products()->each(function (Product $product) use ($productOption) {
                $product->variants()->delete();
                ProductVariant::createByProduct($product);
            });
        } else {
            $productOption->forceDelete();
        }
    }
}
