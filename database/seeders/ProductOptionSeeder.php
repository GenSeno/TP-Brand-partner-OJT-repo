<?php

namespace Database\Seeders;

use App\Models\ProductOption;
use DB;

class ProductOptionSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productOptions = $this->getSeedData('product-options');

        DB::transaction(function () use ($productOptions) {
            foreach ($productOptions as $option) {
                $productOption = ProductOption::firstOrCreate([
                    'name' => $option->name,
                ], [
                    'shared' => true,
                    'autoapply' => true,
                    'permanent' => true,
                    'position' => $option->position
                ]);

                foreach ($option->values as $value) {
                    $productOption->values()->firstOrCreate([
                        'value' => $value->value,
                    ], [
                        'label' => $value->label,
                        'position' => $value->position,
                    ]);
                }
            }
        });
    }
}
