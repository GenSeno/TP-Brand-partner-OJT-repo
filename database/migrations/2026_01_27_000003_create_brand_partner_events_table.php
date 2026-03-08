<?php

use App\Enums\BrandPartnerEventStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_partner_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_partner_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->index()->default(BrandPartnerEventStatus::UPCOMING->value);
            $table->boolean('enabled')->index()->default(true);
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->unique(['brand_partner_id', 'slug']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_partner_events');
    }
};
