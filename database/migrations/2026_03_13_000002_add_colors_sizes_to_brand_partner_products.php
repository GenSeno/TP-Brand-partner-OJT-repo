<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            if (!Schema::hasColumn('brand_partner_products', 'colors')) {
                $table->text('colors')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('brand_partner_products', 'sizes')) {
                $table->text('sizes')->nullable()->after('colors');
            }
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
