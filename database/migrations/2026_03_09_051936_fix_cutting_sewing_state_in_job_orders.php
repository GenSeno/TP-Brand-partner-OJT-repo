<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE job_orders
            SET current_state = 'sewing'
            WHERE current_state = 'cutting/sewing'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE job_orders
            SET current_state = 'cutting/sewing'
            WHERE current_state = 'sewing'
        ");
    }
};
