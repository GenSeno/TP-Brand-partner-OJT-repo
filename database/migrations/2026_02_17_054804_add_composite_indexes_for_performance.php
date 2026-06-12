<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            ['quote_lines',    'quote_lines_purchasable_type_purchasable_id_index',    'purchasable_type, purchasable_id'],
            ['order_lines',    'order_lines_purchasable_type_purchasable_id_index',    'purchasable_type, purchasable_id'],
            ['invoices',       'invoices_order_id_status_index',                       'order_id, status'],
            ['job_orders',     'job_orders_current_state_urgency_flag_index',          'current_state, urgency_flag'],
            ['quote_addresses','quote_addresses_quote_id_type_index',                  'quote_id, type'],
            ['quotes',         'quotes_quotable_type_quotable_id_index',               'quotable_type, quotable_id'],
        ];

        foreach ($indexes as [$table, $name, $columns]) {
            try {
                DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$name}` ({$columns})");
            } catch (\Exception $e) {
                // Index may already exist — skip
            }
        }
    }

    public function down(): void
    {
        Schema::table('quote_lines', function (Blueprint $table) {
            $table->dropIndex(['purchasable_type', 'purchasable_id']);
        });

        Schema::table('order_lines', function (Blueprint $table) {
            $table->dropIndex(['purchasable_type', 'purchasable_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'status']);
        });

        Schema::table('job_orders', function (Blueprint $table) {
            $table->dropIndex(['current_state', 'urgency_flag']);
        });

        Schema::table('quote_addresses', function (Blueprint $table) {
            $table->dropIndex(['quote_id', 'type']);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex(['quotable_type', 'quotable_id']);
        });
    }
};
