<?php

use App\Enums\OrderLineType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quote_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quote_id')->constrained();
            $table->morphs('purchasable');
            $table->unsignedSmallInteger('quantity');
            $table->unsignedBigInteger('purchase_price'); // orig price
            $table->unsignedBigInteger('discount_total');
            $table->decimal('tax_rate', 7, 3);
            $table->unsignedBigInteger('tax_total');
            $table->unsignedBigInteger('unit_cost'); // computed price
            $table->unsignedBigInteger('total')->index(); // unit_cost * quantity
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_lines');
    }
};
