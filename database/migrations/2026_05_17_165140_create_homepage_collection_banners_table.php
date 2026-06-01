<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_collection_banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_partner_id')->constrained()->cascadeOnDelete();
            $table->string('section_type')->default('panel');
            $table->string('title');
            $table->string('badge_text')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_text')->default('VIEW COLLECTION');
            $table->string('overlay_class')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('brand_partner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_collection_banners');
    }
};
