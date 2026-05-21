<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('brand_partner_orders', function (Blueprint $table) {
            $table->string('payment_invoice_id')->nullable();
            $table->string('payment_status')->default('unpaid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_partner_orders', function (Blueprint $table) {
            //
        });
    }
};
