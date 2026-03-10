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
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->index(['status']);
            $table->index(['approval_status']);
            $table->index(['tender_status']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index(['status']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['status']);
        });

        Schema::table('goods_return_requests', function (Blueprint $table) {
            $table->index(['resolution_status']);
            $table->index(['resolution_type']);
        });

        Schema::table('debit_notes', function (Blueprint $table) {
            $table->index(['status']);
        });

        Schema::table('catalogue_items', function (Blueprint $table) {
            $table->index(['company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['approval_status']);
            $table->dropIndex(['tender_status']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('goods_return_requests', function (Blueprint $table) {
            $table->dropIndex(['resolution_status']);
            $table->dropIndex(['resolution_type']);
        });

        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('catalogue_items', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'is_active']);
        });
    }
};
