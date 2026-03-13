<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brand_partner_product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('brand_partner_products')
                ->cascadeOnDelete();
            $table->string('color', 100)->nullable();
            $table->string('color_hex', 7)->nullable(); // e.g. #FF5733
            $table->string('size', 50)->nullable();
            $table->string('sku', 100)->nullable();
            $table->unsignedBigInteger('price')->nullable(); // in cents, overrides product price
            $table->integer('stock')->default(0);
            $table->boolean('track_stock')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_partner_product_variations');
    }
};
