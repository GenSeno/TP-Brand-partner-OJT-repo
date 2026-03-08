<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use DB;

class ProductSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('local', 'testing')) {
            $products = $this->getSeedData('products');

            DB::transaction(function () use ($products) {
                $categories = Category::whereIn('slug', $products->pluck('category')->unique())
                    ->get(['id', 'slug'])
                    ->keyBy('slug');

                $products->chunk(10)->each(function ($products) use ($categories) {
                    $products->each(function ($product) use ($categories) {
                        $productModel = Product::create([
                            'category_id' => $categories[$product->category]->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'status' => $product->status,
                        ]);

                        $productModel->addMediaFromUrl($product->image)
                            ->toMediaCollection('image');
                    });
                });
            });
        }
    }
}
