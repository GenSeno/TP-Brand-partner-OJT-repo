<?php

namespace Database\Seeders;

use App\Models\Category;
use DB;

class CategorySeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = $this->getSeedData('categories');

        DB::transaction(function () use ($categories) {
            foreach ($categories as $category) {
                Category::firstOrCreate([
                    'name' => $category->name,
                    'slug' => $category->slug,
                ], [
                    'enabled' => true,
                ]);
            }
        });
    }
}
