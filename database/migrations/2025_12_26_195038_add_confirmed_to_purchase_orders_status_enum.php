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
        // For PostgreSQL, we need to drop and recreate the check constraint for the enum-like behavior
        DB::statement('ALTER TABLE purchase_orders DROP CONSTRAINT IF EXISTS purchase_orders_status_check');
        DB::statement("ALTER TABLE purchase_orders ADD CONSTRAINT purchase_orders_status_check CHECK (status::text = ANY (ARRAY['issued'::character varying, 'confirmed'::character varying, 'acknowledged'::character varying, 'partial_delivery'::character varying, 'full_delivery'::character varying, 'completed'::character varying, 'cancelled'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE purchase_orders DROP CONSTRAINT IF EXISTS purchase_orders_status_check');
        DB::statement("ALTER TABLE purchase_orders ADD CONSTRAINT purchase_orders_status_check CHECK (status::text = ANY (ARRAY['issued'::character varying, 'acknowledged'::character varying, 'partial_delivery'::character varying, 'full_delivery'::character varying, 'completed'::character varying, 'cancelled'::character varying]::text[]))");
    }
};
