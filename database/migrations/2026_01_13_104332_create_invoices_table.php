<?php

use App\Enums\InvoiceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable()->unique();
            $table->dateTime('invoiced_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->unsignedBigInteger('sub_total')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('down_payment')->default(0);
            $table->unsignedBigInteger('amount_due')->default(0);
            $table->unsignedBigInteger('vatable_amount')->default(0);
            $table->unsignedBigInteger('vat_amount')->default(0);
            $table->string('status')->default(InvoiceStatus::UNPAID->value);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
