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
        Schema::table('quotes', function (Blueprint $table) {
            $table->timestamp('draft_at')->nullable()->after('completed_at');
            $table->timestamp('cancelled_at')->nullable()->after('draft_at');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['draft_at', 'cancelled_at']);
        });
    }
};
