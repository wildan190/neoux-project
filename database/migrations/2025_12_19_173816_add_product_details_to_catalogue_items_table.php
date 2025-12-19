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
        Schema::table('catalogue_items', function (Blueprint $table) {
            $table->unsignedBigInteger('catalogue_product_id')->nullable()->after('id');
            $table->decimal('price', 15, 2)->default(0)->after('sku');
            $table->integer('stock')->default(0)->after('price');
            $table->string('unit')->nullable()->after('stock');
            $table->boolean('is_active')->default(true)->after('unit');

            $table->foreign('catalogue_product_id')->references('id')->on('catalogue_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogue_items', function (Blueprint $table) {
            $table->dropForeign(['catalogue_product_id']);
            $table->dropColumn(['catalogue_product_id', 'price', 'stock', 'unit', 'is_active']);
        });
    }
};
