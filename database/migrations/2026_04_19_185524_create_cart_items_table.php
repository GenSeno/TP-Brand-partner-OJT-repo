<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_partner_product_id')->constrained()->cascadeOnDelete();
            $table->string('color', 100)->nullable();
            $table->string('size', 50)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            // Prevent duplicate entries for the same product+variant per user
            $table->unique(
                ['user_id', 'brand_partner_product_id', 'color', 'size'],
                'cart_items_unique_variant'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};