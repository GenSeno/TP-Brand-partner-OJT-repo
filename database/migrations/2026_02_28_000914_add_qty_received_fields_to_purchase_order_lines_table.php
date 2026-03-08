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
        Schema::table('purchase_order_lines', function (Blueprint $table) {
            $table->integer('qty_received')->default(0)->after('total');
        });
    }

    public function down()
    {
        Schema::table('purchase_order_lines', function (Blueprint $table) {
            $table->dropColumn(['qty_received']);
        });
    }
};
