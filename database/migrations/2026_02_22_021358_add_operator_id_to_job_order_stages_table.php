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
        Schema::table('job_order_stages', function (Blueprint $table) {
            $table->foreignId('operator_id')->nullable()->after('due_at')
                ->constrained('staff')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_order_stages', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Staff::class, 'operator_id');
            $table->dropColumn('operator_id');
        });
    }
};
