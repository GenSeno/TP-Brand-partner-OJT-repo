<?php

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->after('sku', function (Blueprint $table) {
                $table->boolean('enabled')->default(true);
                $table->boolean('default')->default(false);
                $table->boolean('has_transactions')->default(false);
                $table->string('uom_code', 8)->default('pc');
            });
        });

        Product::get()->each(function (Product $product) {
            ProductVariant::where('product_id', $product->id)->orderBy('id')
                ->first()?->update(['default' => true]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'enabled',
                'default',
                'has_transactions',
                'uom_code',
            ]);
        });
    }
};
