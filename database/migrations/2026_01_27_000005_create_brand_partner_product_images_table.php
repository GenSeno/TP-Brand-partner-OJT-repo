<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_partner_product_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('brand_partner_products')->cascadeOnDelete();
            $table->string('path', 500);
            $table->string('alt_text')->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            $table->index('product_id');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_partner_product_images');
    }
};
