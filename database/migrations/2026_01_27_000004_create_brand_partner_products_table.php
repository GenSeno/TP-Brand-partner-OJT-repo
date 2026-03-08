<?php

use App\Enums\BrandPartnerProductStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_partner_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('brand_partner_categories')->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('brand_partner_events')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('compare_price')->nullable();
            $table->string('sku', 100)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('track_stock')->default(false);
            $table->string('status')->index()->default(BrandPartnerProductStatus::DRAFT->value);
            $table->boolean('featured')->index()->default(false);
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['brand_partner_id', 'slug']);
            $table->index('event_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_partner_products');
    }
};
