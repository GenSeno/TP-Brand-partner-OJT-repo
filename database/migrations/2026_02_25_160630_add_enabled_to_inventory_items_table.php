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
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->boolean('enabled')->default(1)->after('notes');
            $table->unsignedInteger('reorder_qty')
                  ->default(0)
                  ->after('enabled');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('enabled');
            $table->dropColumn('reorder_qty');
        });
    }
};
