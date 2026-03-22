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
        Schema::create('brand_partner_collections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_partner_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('brand_partner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_partner_collections');
    }
};
