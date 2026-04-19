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
        Schema::create('procurement_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('vendor_company_id')->constrained('companies')->onDelete('cascade');
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('draft'); // draft, active, expired, terminated
            $table->foreignUuid('source_po_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
            $table->foreignUuid('created_by_user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_contract_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contract_id')->constrained('procurement_contracts')->onDelete('cascade');
            $table->foreignId('catalogue_item_id')->constrained('catalogue_items')->onDelete('cascade');
            $table->decimal('fixed_price', 15, 2);
            $table->string('currency')->default('IDR');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_contract_items');
        Schema::dropIfExists('procurement_contracts');
    }
};
