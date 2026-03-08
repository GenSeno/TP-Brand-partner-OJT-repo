<?php

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')->constrained();
            $table->string('reference')->nullable()->unique();
            $table->date('order_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->string('status')->index()->default(PurchaseOrderStatus::DRAFT->value);
            $table->unsignedBigInteger('sub_total')->index();
            $table->json('discount_breakdown')->nullable();
            $table->unsignedBigInteger('discount_total')->index();
            $table->json('tax_breakdown');
            $table->unsignedBigInteger('tax_total')->index();
            $table->unsignedBigInteger('total')->index();
            $table->text('notes')->nullable();
            $table->string('currency_code', 3);
            $table->string('compare_currency_code', 3)->nullable();
            $table->decimal('exchange_rate', 10, 4)->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
