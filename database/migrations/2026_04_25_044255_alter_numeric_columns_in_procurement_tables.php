<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Alter purchase_orders.total_amount from numeric(15,2) to numeric(20,2)
        DB::statement('ALTER TABLE purchase_orders ALTER COLUMN total_amount TYPE numeric(20,2)');

        // Alter purchase_order_items numeric columns from numeric(15,2) to numeric(20,2)
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN unit_price TYPE numeric(20,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN tax_amount TYPE numeric(20,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN tax_rate TYPE numeric(10,4)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN total_inc_tax TYPE numeric(20,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN price_idr TYPE numeric(20,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN price_original TYPE numeric(20,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN subtotal TYPE numeric(20,2)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE purchase_orders ALTER COLUMN total_amount TYPE numeric(15,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN unit_price TYPE numeric(15,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN tax_amount TYPE numeric(15,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN tax_rate TYPE numeric(5,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN total_inc_tax TYPE numeric(15,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN price_idr TYPE numeric(15,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN price_original TYPE numeric(15,2)');
        DB::statement('ALTER TABLE purchase_order_items ALTER COLUMN subtotal TYPE numeric(15,2)');
    }
};
