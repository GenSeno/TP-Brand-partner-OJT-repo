<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->foreignId('collection_id')
                ->nullable()
                ->after('category_id')
                ->constrained('brand_partner_product_option_values')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\BrandPartnerProductOptionValue::class, 'collection_id');
            $table->dropColumn('collection_id');
        });
    }
};
