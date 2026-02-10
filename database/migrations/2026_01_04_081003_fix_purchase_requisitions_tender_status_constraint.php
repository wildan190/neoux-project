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
        // Fix tender_status in purchase_requisitions
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE purchase_requisitions DROP CONSTRAINT IF EXISTS purchase_requisitions_tender_status_check');
        }

        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->string('tender_status')->default('open')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->enum('tender_status', ['open', 'closed', 'awarded'])->default('open')->change();
        });
    }
};
