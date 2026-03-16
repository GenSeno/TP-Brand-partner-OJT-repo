<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->string('approval_status')->default('pending')->after('status');
            $table->text('approval_notes')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approval_notes');
        });
    }

    public function down(): void
    {
        Schema::table('brand_partner_products', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approval_notes', 'approved_at']);
        });
    }
};
