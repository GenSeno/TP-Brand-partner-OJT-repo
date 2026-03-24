<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_partner_orders', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->string('jo_number')->nullable()->after('notes');
            $table->string('jo_status')->nullable()->after('jo_number');
        });
    }

    public function down(): void
    {
        Schema::table('brand_partner_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'jo_number', 'jo_status']);
        });
    }
};
