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
        Schema::table('procurement_contracts', function (Blueprint $table) {
            $table->timestamp('vendor_signed_at')->nullable();
            $table->foreignUuid('vendor_signed_by_user_id')->nullable()->constrained('users');
            $table->timestamp('buyer_approved_at')->nullable();
            $table->foreignUuid('buyer_approved_by_user_id')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurement_contracts', function (Blueprint $table) {
            //
        });
    }
};
