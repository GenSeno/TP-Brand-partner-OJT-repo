<?php
use App\Enums\ExpenseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique();
            $table->foreignId('supplier_id')
                  ->constrained('suppliers');
            $table->dateTime('expense_date')->nullable()->index();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('sub_total')->default(0);
            $table->json('discount_breakdown')->nullable();
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->foreignId('currency_id')->constrained();

            
            // payment info
            $table->dateTime('payment_date')->nullable()->index();
            $table->string('payment_method')->nullable();
            $table->string('paid_to')->nullable();
            $table->string('reference_no')->nullable();
            // status
            $table->dateTime('cancelled_at')->nullable()->index();
            $table->dateTime('upcoming_at')->nullable()->index();
            $table->dateTime('draft_at')->nullable()->index();
            
            $table->string('status')->index()->default(ExpenseStatus::DRAFT->value);
            $table->timestamps();

            // helpful indexes
            $table->index('reference');
            $table->index(['expense_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

