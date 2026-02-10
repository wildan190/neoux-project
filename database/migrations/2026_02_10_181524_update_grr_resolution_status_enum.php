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
        // For Postgres, we can't easily change enums without dropping constraints.
        // We'll change it to string for better flexibility in this cycle.
        Schema::table('goods_return_requests', function (Blueprint $table) {
            $table->string('resolution_status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_return_requests', function (Blueprint $table) {
            // Revert to original enum if needed, though rollback might be complex if new values exist.
            $table->enum('resolution_status', ['pending', 'approved_by_vendor', 'rejected_by_vendor', 'resolved'])->default('pending')->change();
        });
    }
};
