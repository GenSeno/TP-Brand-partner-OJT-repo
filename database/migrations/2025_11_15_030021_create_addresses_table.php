<?php

use App\Enums\AddressType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->morphs('addressable'); // either user or customer
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('type')->default(AddressType::SHIPPING->value);
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name')->nullable();
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('barangay')->nullable(); // or line3
            $table->string('city');
            $table->string('province')->nullable(); // or state
            $table->string('postcode')->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('default')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
