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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalogue_item_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete(); // Who did it
            $table->enum('type', ['in', 'out']); // in = addition, out = deduction
            $table->integer('quantity');
            $table->integer('previous_stock'); // Snapshot before change
            $table->integer('current_stock'); // Snapshot after change
            $table->string('reference_type')->nullable(); // e.g. 'goods_receipt', 'sales_order', 'manual'
            $table->string('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
