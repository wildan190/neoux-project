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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignUuid('purchase_requisition_id')->nullable()->change();
            $table->foreignUuid('offer_id')->nullable()->change();
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreignUuid('purchase_requisition_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignUuid('purchase_requisition_id')->nullable(false)->change();
            $table->foreignUuid('offer_id')->nullable(false)->change();
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreignUuid('purchase_requisition_item_id')->nullable(false)->change();
        });
    }
};
