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
        Schema::create('expense_lines', function (Blueprint $table) {
            $table->id();

            // voucher header
            $table->foreignId('expense_id')
                  ->constrained('expenses')
                  ->cascadeOnDelete();

            // expense account per line
            $table->foreignId('expense_account_id')
                  ->constrained('expense_accounts');

      
           $table->longText('description')->nullable();
            $table->unsignedSmallInteger('quantity');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('total')->index(); 
            $table->timestamps();

            // indexes
            $table->index(['expense_id', 'expense_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_lines');
    }
};
