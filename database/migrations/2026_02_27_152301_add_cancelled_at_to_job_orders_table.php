<?php

use App\Models\JobOrder;
use App\States\JobOrderState\Cancelled;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_orders', function (Blueprint $table) {
            $table->dateTime('cancelled_at')->nullable()->after('due_at');
        });

        JobOrder::query()
            ->where('current_state', Cancelled::$name)
            ->update(['cancelled_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        JobOrder::query()
            ->whereNotNull('cancelled_at')
            ->update(['current_state' => Cancelled::$name]);

        Schema::table('job_orders', function (Blueprint $table) {
            $table->dropColumn('cancelled_at');
        });
    }
};
