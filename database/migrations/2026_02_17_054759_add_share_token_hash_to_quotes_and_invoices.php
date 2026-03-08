<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('share_token_hash', 8)->nullable()->index()->after('reference');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('share_token_hash', 8)->nullable()->index()->after('reference');
        });

        // Backfill existing records
        $appKey = config('app.key');

        \App\Models\Quote::whereNotNull('reference')->each(function ($quote) use ($appKey) {
            $quote->update([
                'share_token_hash' => substr(hash_hmac('sha256', $quote->reference, $appKey), 0, 8),
            ]);
        });

        \App\Models\Invoice::whereNotNull('reference')->each(function ($invoice) use ($appKey) {
            $invoice->update([
                'share_token_hash' => substr(hash_hmac('sha256', $invoice->reference, $appKey), 0, 8),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('share_token_hash');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('share_token_hash');
        });
    }
};
