<?php

use App\Enums\BrandPartnerOrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_partner_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference', 50)->unique()->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 50)->nullable();
            $table->string('status')->index()->default(BrandPartnerOrderStatus::PENDING->value);
            $table->unsignedBigInteger('sub_total');
            $table->unsignedBigInteger('tax_total')->default(0);
            $table->unsignedBigInteger('total');
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index('brand_partner_id');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_partner_orders');
    }
};
