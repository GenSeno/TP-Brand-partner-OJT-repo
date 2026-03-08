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
        // Regions table
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('psgc_code')->unique();
            $table->string('region_name');
            $table->string('region_code');
            $table->timestamps();
        });

        // Provinces table
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('psgc_code')->unique();
            $table->string('province_name');
            $table->string('province_code');
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Cities table
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('psgc_code')->unique();
            $table->string('city_name');
            $table->string('city_code');
            $table->enum('city_type', ['city', 'municipality']);
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Barangays table
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->string('psgc_code')->unique();
            $table->string('barangay_name');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('regions');
    }
};
