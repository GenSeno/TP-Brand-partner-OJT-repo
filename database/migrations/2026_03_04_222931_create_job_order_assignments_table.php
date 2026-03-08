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
        Schema::create('job_order_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->string('state')->index();
            $table->unsignedSmallInteger('quantity');
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->unique(['order_line_id', 'staff_id'], 'unique_order_line_staff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_assignments');
    }
};
