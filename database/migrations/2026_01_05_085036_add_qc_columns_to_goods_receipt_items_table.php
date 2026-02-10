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
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->integer('quantity_good')->default(0)->after('quantity_received');
            $table->integer('quantity_rejected')->default(0)->after('quantity_good');
            $table->string('rejected_reason')->nullable()->after('quantity_rejected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['quantity_good', 'quantity_rejected', 'rejected_reason']);
        });
    }
};
