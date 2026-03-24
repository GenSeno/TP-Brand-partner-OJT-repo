<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_partner_orders', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('customer_name');
            $table->text('address')->nullable()->after('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('brand_partner_orders', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'address']);
        });
    }
};
