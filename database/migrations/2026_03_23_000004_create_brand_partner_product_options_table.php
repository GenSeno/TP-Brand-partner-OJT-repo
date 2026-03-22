<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_partner_product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_partner_id')->constrained('brand_partners')->cascadeOnDelete();
            $table->string('name');
            $table->integer('position')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_partner_product_options');
    }
};
