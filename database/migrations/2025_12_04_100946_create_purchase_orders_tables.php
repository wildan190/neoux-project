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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('po_number')->unique();
            $table->foreignUuid('purchase_requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('offer_id')->constrained('purchase_requisition_offers');
            $table->unsignedBigInteger('vendor_company_id');
            $table->foreign('vendor_company_id')->references('id')->on('companies');
            $table->foreignUuid('created_by_user_id')->constrained('users');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['issued', 'acknowledged', 'partial_delivery', 'full_delivery', 'completed', 'cancelled'])->default('issued');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('purchase_requisition_item_id')->constrained('purchase_requisition_items');
            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
