<?php

use App\Enums\QuoteStatus;
use App\Enums\QuoteType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            $table->nullableMorphs('quotable'); // either user, customer, or guest
            $table->foreignId('currency_id')->constrained();
            $table->foreignId('merged_id')->nullable()->constrained('quotes'); // merging quotes for different owners
            $table->foreignId('order_id')->nullable()->constrained(); // completed order
            $table->string('reference')->nullable()->unique();
            $table->longText('description')->nullable();
            $table->string('type')->index()->default(QuoteType::ONLINE->value);
            $table->string('status')->index()->default(QuoteStatus::REQUEST->value);

            $table->unsignedBigInteger('sub_total')->default(0);
            $table->json('discount_breakdown')->nullable();
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->json('shipping_breakdown')->nullable();
            $table->unsignedBigInteger('shipping_total')->default(0);
            $table->json('tax_breakdown')->nullable();
            $table->unsignedBigInteger('tax_total')->default(0);
            $table->unsignedBigInteger('total')->default(0)->index();
            $table->string('payment_terms')->nullable();
            $table->longText('general_terms')->nullable(); // html content
            $table->unsignedBigInteger('late_payment_charges')->default(0);
            $table->unsignedInteger('estimated_lead_time')->default(0); // in days

            $table->dateTime('quoted_at')->nullable()->index(); // customer-facing date (for admin reference)
            $table->dateTime('expired_at')->nullable()->index();
            $table->dateTime('completed_at')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
