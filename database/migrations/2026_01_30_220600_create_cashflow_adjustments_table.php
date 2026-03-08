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
        Schema::create('cashflow_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('group'); // cash, bank, etc.

            $table->enum('method', ['in', 'out']); // cash in / cash out
            $table->unsignedBigInteger('amount'); // store in cents
            $table->string('description')->nullable();
            $table->string('posted_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_adjustments');
    }
};
