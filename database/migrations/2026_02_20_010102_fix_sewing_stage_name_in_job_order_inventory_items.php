<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The previous backfill used 'sewing' but the actual morph class is 'cutting/sewing'
        DB::statement("
            UPDATE job_order_inventory_items
            SET stage = 'cutting/sewing'
            WHERE stage = 'sewing'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE job_order_inventory_items
            SET stage = 'sewing'
            WHERE stage = 'cutting/sewing'
        ");
    }
};
