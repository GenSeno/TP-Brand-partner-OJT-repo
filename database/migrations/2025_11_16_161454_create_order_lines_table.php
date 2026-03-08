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
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained();
            $table->morphs('purchasable');
            $table->string('type')->index();
            $table->string('description');
            $table->string('option')->nullable();
            $table->string('identifier')->index();
            $table->unsignedBigInteger('unit_price')->index();
            $table->unsignedSmallInteger('unit_quantity')->default(1)->index();
            $table->unsignedSmallInteger('quantity');
            $table->unsignedBigInteger('sub_total')->index();
            $table->unsignedBigInteger('discount_total')->default(0)->index();
            $table->json('tax_breakdown')->nullable();
            $table->unsignedBigInteger('tax_total')->index();
            $table->unsignedBigInteger('total')->index();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
