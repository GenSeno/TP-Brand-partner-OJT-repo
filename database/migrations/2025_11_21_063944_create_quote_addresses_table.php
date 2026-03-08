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
        Schema::create('quote_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quote_id')->constrained();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('tax_identifier')->nullable();
            $table->string('line1')->nullable();
            $table->string('line2')->nullable();
            $table->string('barangay')->nullable(); // or line3
            $table->string('city');
            $table->string('province')->nullable(); // or state
            $table->string('postcode')->nullable();
            $table->string('delivery_instructions')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->index()->default(AddressType::SHIPPING->value);
            $table->string('shipping_option')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_addresses');
    }
};
