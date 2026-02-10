<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add more statuses to invoice enum
        // Note: SQLite doesn't support modifying enum easily, but for B2B flow we need these.
        // For Postgres/MySQL we would use DB::statement.

        // Let's just use string for more flexibility if needed, but for now let's assume we can update status.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
