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
        Schema::table('invoices', function (Blueprint $table) {
            $table->after('reference', function (Blueprint $table) {
                $table->string('description')->nullable();
                $table->string('type')
                    ->nullable()
                    ->index();
            });

            $table->dropColumn('down_payment');
        });

        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->string('type')->nullable()->index()->after('invoice_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('internal_reference')->nullable()->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['description', 'type']);
            $table->unsignedBigInteger('down_payment')->default(0)->after('total');
        });

        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('internal_reference');
        });
    }
};
