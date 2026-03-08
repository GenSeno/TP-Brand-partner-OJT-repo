<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('job_order_inventory_items', function (Blueprint $table) {
            // Fix: integer truncates decimals (e.g. 2.5m of fabric became 2)
            $table->decimal('amount_used', 10, 3)->default(0)->change();

            // Add stage so each stage's consumption is tracked separately
            $table->string('stage')->nullable()->after('inventory_item_id');
        });

        // Backfill stage for existing rows using the inventory item's type
        DB::statement("
            UPDATE job_order_inventory_items joii
            JOIN inventory_items ii ON joii.inventory_item_id = ii.id
            SET joii.stage = CASE ii.type
                WHEN 'subli paper' THEN 'printing'
                WHEN 'fabric'      THEN 'heat-press'
                WHEN 'thread'      THEN 'sewing'
                WHEN 'packaging'   THEN 'packing'
                ELSE NULL
            END
        ");
    }

    public function down(): void
    {
        Schema::table('job_order_inventory_items', function (Blueprint $table) {
            $table->unsignedInteger('amount_used')->default(0)->change();
            $table->dropColumn('stage');
        });
    }
};
