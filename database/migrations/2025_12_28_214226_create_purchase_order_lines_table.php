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
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_order_id')->constrained();
            $table->string('description');
            $table->unsignedBigInteger('unit_price')->index();
            $table->unsignedSmallInteger('unit_quantity')->index()->default(1);
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
