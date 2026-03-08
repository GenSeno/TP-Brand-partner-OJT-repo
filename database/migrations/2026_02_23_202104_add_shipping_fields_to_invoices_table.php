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
        Schema::table('invoices', function (Blueprint $table) {
            $table->json('shipping_breakdown')->nullable()->after('status'); 
            $table->integer('shipping_total')
                ->default(0)
                ->unsigned()
                ->index()
                ->after('shipping_breakdown');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['shipping_breakdown', 'shipping_total']);
        });
    }
};
