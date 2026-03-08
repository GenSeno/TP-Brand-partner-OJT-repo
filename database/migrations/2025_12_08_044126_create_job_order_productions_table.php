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
        Schema::create('job_order_productions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_order_id')->constrained();
            $table->foreignId('order_line_id')->constrained();
            $table->string('state')->index();
            $table->unsignedSmallInteger('quantity');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_productions');
    }
};
