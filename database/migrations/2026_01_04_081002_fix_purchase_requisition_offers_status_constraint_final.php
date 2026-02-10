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
        // For PostgreSQL, changing an enum doesn't drop the check constraint automatically.
        // We must drop it manually.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE purchase_requisition_offers DROP CONSTRAINT IF EXISTS purchase_requisition_offers_status_check');
        }

        Schema::table('purchase_requisition_offers', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requisition_offers', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->change();
        });
    }
};
