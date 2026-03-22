<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_partner_product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')
                ->constrained('brand_partner_product_options')
                ->cascadeOnDelete();
            $table->string('label');
            $table->string('value')->nullable();
            $table->integer('position')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_partner_product_option_values');
    }
};
