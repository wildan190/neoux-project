<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Explicitly drop the check constraint for PostgreSQL
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE goods_return_requests DROP CONSTRAINT IF EXISTS goods_return_requests_resolution_status_check');
            DB::statement('ALTER TABLE goods_return_requests DROP CONSTRAINT IF EXISTS goods_return_requests_issue_type_check');
            DB::statement('ALTER TABLE goods_return_requests DROP CONSTRAINT IF EXISTS goods_return_requests_resolution_type_check');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback is complex as we don't know the exact previous enum values here without hardcoding,
        // and we already changed the column to string.
    }
};
