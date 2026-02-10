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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->timestamp('vendor_accepted_at')->nullable()->after('confirmed_at');
            $table->timestamp('vendor_rejected_at')->nullable()->after('vendor_accepted_at');
            $table->text('vendor_notes')->nullable()->after('vendor_rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['vendor_accepted_at', 'vendor_rejected_at', 'vendor_notes']);
        });
    }
};
