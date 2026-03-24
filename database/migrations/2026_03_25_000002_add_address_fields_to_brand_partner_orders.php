<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_partner_orders', function (Blueprint $table) {
            $table->string('address_line1')->nullable()->after('address');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('barangay')->nullable()->after('address_line2');
            $table->string('city')->nullable()->after('barangay');
            $table->string('province')->nullable()->after('city');
            $table->string('postcode')->nullable()->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('brand_partner_orders', function (Blueprint $table) {
            $table->dropColumn(['address_line1', 'address_line2', 'barangay', 'city', 'province', 'postcode']);
        });
    }
};
