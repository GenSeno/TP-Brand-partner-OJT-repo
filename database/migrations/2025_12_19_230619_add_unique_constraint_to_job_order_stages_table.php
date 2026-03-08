<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_order_stages', function (Blueprint $table) {
            $table->dropIndex(['state']);
            $table->unique(['job_order_id', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_order_stages', function (Blueprint $table) {
            $table->dropForeign(['job_order_id']);
            $table->dropUnique(['job_order_id', 'state']);

            $table->foreign('job_order_id')
                ->references('id')
                ->on('job_orders');

            $table->index('state');
        });
    }
};
