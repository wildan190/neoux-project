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
        Schema::create('purchase_requisition_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('purchase_requisition_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->decimal('total_price', 15, 2);
            $table->text('notes')->nullable();
            $table->decimal('rank_score', 5, 2)->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->timestamps();

            // Ensure one offer per company per PR
            $table->unique(['purchase_requisition_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_offers');
    }
};
