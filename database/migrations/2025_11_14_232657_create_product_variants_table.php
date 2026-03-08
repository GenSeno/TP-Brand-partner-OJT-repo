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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained();
            $table->foreignId('media_id')->nullable()->constrained();
            $table->unsignedInteger('unit_quantity')->index()->default(1);
            $table->unsignedInteger('min_quantity')->index()->default(1);
            $table->unsignedInteger('quantity_increment')->index()->default(1);
            $table->string('sku')->unique();
            $table->decimal('length_value', 10, 4)->index()->default(0);
            $table->string('length_unit', 10)->default('mm');
            $table->decimal('width_value', 10, 4)->index()->default(0);
            $table->string('width_unit', 10)->default('mm');
            $table->decimal('height_value', 10, 4)->index()->default(0);
            $table->string('height_unit', 10)->default('mm');
            $table->decimal('weight_value', 10, 4)->index()->default(0);
            $table->string('weight_unit', 10)->default('mm');
            $table->decimal('volume_value', 10, 4)->index()->default(0);
            $table->string('volume_unit', 10)->default('mm');
            $table->boolean('shippable')->index()->default(true);
            $table->integer('stock')->index()->default(0);
            $table->integer('backorder')->index()->default(0);
            $table->boolean('purchasable')->index()->default(true);
            $table->string('barcode')->unique()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
