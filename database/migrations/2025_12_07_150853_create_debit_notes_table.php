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
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('dn_number', 50)->unique();
            $table->uuid('goods_return_request_id');
            $table->uuid('purchase_order_id');
            $table->decimal('original_amount', 15, 2);
            $table->decimal('adjusted_amount', 15, 2);
            $table->decimal('deduction_amount', 15, 2);
            $table->text('reason')->nullable();
            $table->timestamp('approved_by_vendor_at')->nullable();
            $table->timestamps();

            $table->foreign('goods_return_request_id')->references('id')->on('goods_return_requests')->onDelete('cascade');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_notes');
    }
};
