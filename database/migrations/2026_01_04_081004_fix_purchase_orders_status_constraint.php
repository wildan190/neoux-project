<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix status in purchase_orders
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE purchase_orders DROP CONSTRAINT IF EXISTS purchase_orders_status_check');
        }

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('status')->default('issued')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['issued', 'acknowledged', 'partial_delivery', 'full_delivery', 'completed', 'cancelled'])->default('issued')->change();
        });
    }
};
