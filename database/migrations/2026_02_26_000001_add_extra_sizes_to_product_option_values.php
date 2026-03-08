<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sizeOption = DB::table('product_options')
            ->where('name', 'Size')
            ->whereNull('deleted_at')
            ->first();

        if (! $sizeOption) {
            return;
        }

        $existing = DB::table('product_option_values')
            ->where('product_option_id', $sizeOption->id)
            ->pluck('value')
            ->toArray();

        $now = now();

        $newSizes = [
            ['label' => '4XL',   'value' => '4XL',    'position' => 9],
            ['label' => '5XL',   'value' => '5XL',    'position' => 10],
            ['label' => 'Others','value' => 'OTHERS',  'position' => 11],
        ];

        foreach ($newSizes as $size) {
            if (! in_array($size['value'], $existing)) {
                DB::table('product_option_values')->insert([
                    'product_option_id' => $sizeOption->id,
                    'label'             => $size['label'],
                    'value'             => $size['value'],
                    'position'          => $size['position'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('product_option_values')
            ->whereIn('value', ['4XL', '5XL', 'OTHERS'])
            ->update(['deleted_at' => now()]);
    }
};
