<?php

use App\Enums\JobOrderUrgency;
use App\States\JobOrderState\NewOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained();
            $table->string('reference')->nullable()->unique();
            $table->string('urgency_flag')
                ->index()
                ->default(JobOrderUrgency::NORMAL->value)
                ->comment('Lowest value = highest urgency');
            $table->string('current_state')->index()->default(NewOrder::$name);
            $table->unsignedSmallInteger('lead_time')
                ->default(0)
                ->comment('in days');
            $table->dateTime('estimated_delivery')->nullable()->index();
            $table->dateTime('ordered_at')->nullable()->index();
            $table->dateTime('due_at')->nullable()->index();
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_orders');
    }
};
