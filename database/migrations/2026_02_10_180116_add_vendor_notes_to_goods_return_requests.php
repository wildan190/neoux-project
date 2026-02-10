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
        Schema::table('goods_return_requests', function (Blueprint $table) {
            $table->text('vendor_notes')->nullable()->after('resolution_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_return_requests', function (Blueprint $table) {
            $table->dropColumn('vendor_notes');
        });
    }
};
