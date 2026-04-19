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
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->foreignUuid('source_po_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
            $table->foreignUuid('contract_id')->nullable()->constrained('procurement_contracts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->dropColumn(['source_po_id', 'contract_id']);
        });
    }
};
