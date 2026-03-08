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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->nullableMorphs('orderable'); // user, customer or guest
            $table->boolean('new_customer')->default(false);
            $table->string('status')->index();
            $table->string('reference')->nullable()->unique();
            $table->integer('sub_total')->unsigned()->index();
            $table->json('discount_breakdown')->nullable();
            $table->integer('discount_total')->default(0)->unsigned()->index();
            $table->json('shipping_breakdown')->nullable();
            $table->integer('shipping_total')->default(0)->unsigned()->index();
            $table->json('tax_breakdown')->nullable();
            $table->integer('tax_total')->unsigned()->index();
            $table->integer('total')->unsigned()->index();
            $table->text('notes')->nullable();
            $table->string('currency_code', 3);
            $table->string('compare_currency_code', 3)->nullable();
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->dateTime('placed_at')->nullable()->index();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
