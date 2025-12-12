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
        Schema::create('goods_return_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('grr_number', 50)->unique();
            $table->uuid('goods_receipt_item_id');
            $table->enum('issue_type', ['damaged', 'rejected', 'wrong_item']);
            $table->integer('quantity_affected');
            $table->text('issue_description')->nullable();
            $table->json('photo_evidence')->nullable();
            $table->enum('resolution_type', ['price_adjustment', 'replacement', 'return_refund'])->nullable();
            $table->enum('resolution_status', ['pending', 'approved_by_vendor', 'rejected_by_vendor', 'resolved'])->default('pending');
            $table->uuid('created_by');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('goods_receipt_item_id')->references('id')->on('goods_receipt_items')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_return_requests');
    }
};
