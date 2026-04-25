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
            $table->string('purchase_type')->nullable()->after('po_number');
            $table->string('dept')->nullable()->after('purchase_type');
            $table->string('month')->nullable()->after('dept');
            $table->string('currency')->nullable()->default('IDR')->after('month');
            $table->string('purchase_company_no')->nullable()->after('company_id');
            $table->string('purchase_company_email')->nullable()->after('purchase_company_no');
            $table->foreignUuid('approved_by_user_id')->nullable()->after('created_by_user_id')->constrained('users');
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('item_name');
            $table->string('business_category')->nullable()->after('unit');
            $table->string('category')->nullable()->after('business_category');
            $table->text('specifications')->nullable()->after('category');
            $table->decimal('tax_amount', 15, 2)->nullable()->after('unit_price');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('tax_amount');
            $table->decimal('total_inc_tax', 15, 2)->nullable()->after('tax_rate');
            $table->decimal('price_idr', 15, 2)->nullable()->after('total_inc_tax');
            $table->decimal('price_original', 15, 2)->nullable()->after('price_idr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by_user_id']);
            $table->dropColumn([
                'purchase_type',
                'dept',
                'month',
                'currency',
                'purchase_company_no',
                'purchase_company_email',
                'approved_by_user_id',
            ]);
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit',
                'business_category',
                'category',
                'specifications',
                'tax_amount',
                'tax_rate',
                'total_inc_tax',
                'price_idr',
                'price_original',
            ]);
        });
    }
};
