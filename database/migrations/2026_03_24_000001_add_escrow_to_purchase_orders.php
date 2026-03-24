<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('escrow_status')->default('pending')->after('status');
            // pending = not yet paid, paid = buyer deposited, released = funds sent to vendor, refunded = returned to buyer, disputed = under review
            $table->timestamp('escrow_paid_at')->nullable()->after('escrow_status');
            $table->timestamp('escrow_released_at')->nullable()->after('escrow_paid_at');
            $table->string('escrow_reference')->nullable()->after('escrow_released_at');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['escrow_status', 'escrow_paid_at', 'escrow_released_at', 'escrow_reference']);
        });
    }
};
