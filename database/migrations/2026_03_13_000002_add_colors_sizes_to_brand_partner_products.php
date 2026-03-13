<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->text('colors')->nullable()->after('sku'); // comma-separated, e.g. "Red,Blue,Green"
            $table->text('sizes')->nullable()->after('colors');  // comma-separated, e.g. "S,M,L,XL"
        });

        Schema::dropIfExists('brand_partner_product_variations');
    }

    public function down(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->dropColumn(['colors', 'sizes']);
        });
    }
};
