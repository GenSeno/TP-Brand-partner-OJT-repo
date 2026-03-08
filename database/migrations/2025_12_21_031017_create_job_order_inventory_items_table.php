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
        Schema::create('job_order_inventory_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_order_id')->constrained();
            $table->foreignId('inventory_item_id')->constrained();
            $table->unsignedInteger('amount_used');

            $table->timestamps();

            $table->unique(['job_order_id', 'inventory_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_inventory_items');
    }
};
