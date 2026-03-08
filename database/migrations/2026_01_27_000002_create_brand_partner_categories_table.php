<?php

use App\Enums\BrandPartnerCategoryType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_partner_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_partner_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('type')->default(BrandPartnerCategoryType::REGULAR->value);
            $table->boolean('enabled')->index()->default(true);
            $table->integer('position')->default(0);

            $table->timestamps();

            $table->unique(['brand_partner_id', 'slug']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_partner_categories');
    }
};
