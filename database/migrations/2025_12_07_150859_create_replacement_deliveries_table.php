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
        Schema::create('replacement_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rd_number', 50)->unique();
            $table->uuid('goods_return_request_id');
            $table->uuid('original_goods_receipt_id');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->enum('status', ['pending', 'shipped', 'received', 'cancelled'])->default('pending');
            $table->string('tracking_number', 100)->nullable();
            $table->uuid('received_by')->nullable();
            $table->timestamps();

            $table->foreign('goods_return_request_id')->references('id')->on('goods_return_requests')->onDelete('cascade');
            $table->foreign('original_goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replacement_deliveries');
    }
};
