<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });

        // Null out category_id values that don't exist in brand_partner_product_option_values
        DB::statement('
            UPDATE brand_partner_products
            SET category_id = NULL
            WHERE category_id IS NOT NULL
              AND category_id NOT IN (SELECT id FROM brand_partner_product_option_values)
        ');

        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('brand_partner_product_option_values')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->foreign('category_id')
                ->references('id')
                ->on('brand_partner_categories')
                ->cascadeOnDelete();
        });
    }
};
