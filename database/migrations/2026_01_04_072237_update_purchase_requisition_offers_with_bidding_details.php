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
        Schema::table('purchase_requisition_offers', function (Blueprint $table) {
            $table->string('delivery_time')->nullable()->after('notes');
            $table->text('warranty')->nullable()->after('delivery_time');
            $table->text('payment_scheme')->nullable()->after('warranty');
            $table->string('bidding_status')->default('pending')->after('status'); // pending, negotiated, winner, lost
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requisition_offers', function (Blueprint $table) {
            $table->dropColumn(['delivery_time', 'warranty', 'payment_scheme', 'bidding_status']);
        });
    }
};
