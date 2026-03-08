<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->json('tax_breakdown')->nullable()->change();
            $table->unsignedBigInteger('sub_total')->default(0)->change();
            $table->unsignedBigInteger('discount_total')->default(0)->change();
            $table->unsignedBigInteger('tax_total')->default(0)->change();
            $table->unsignedBigInteger('total')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->json('tax_breakdown')->nullable(false)->change();
            $table->unsignedBigInteger('sub_total')->default(null)->change();
            $table->unsignedBigInteger('discount_total')->default(null)->change();
            $table->unsignedBigInteger('tax_total')->default(null)->change();
            $table->unsignedBigInteger('total')->default(null)->change();
        });
    }
};
