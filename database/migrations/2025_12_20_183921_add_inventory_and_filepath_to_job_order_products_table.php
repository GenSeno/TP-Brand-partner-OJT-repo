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
        Schema::table('job_order_products', function (Blueprint $table) {
            $table->after('product_id', function (Blueprint $table) {
                $table->foreignId('inventory_item_id')->nullable()->constrained();
                $table->string('file_path')->nullable();
            });

            $table->dropColumn(['fabric_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_order_products', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn(['inventory_item_id', 'file_path']);

            $table->string('fabric_type')->nullable();
        });
    }
};
