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
        Schema::table('companies', function (Blueprint $table) {
            $table->uuid('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->uuid('declined_by')->nullable()->after('approved_at');
            $table->timestamp('declined_at')->nullable()->after('declined_by');

            $table->foreign('approved_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('declined_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['declined_by']);
            $table->dropColumn(['approved_by', 'approved_at', 'declined_by', 'declined_at']);
        });
    }
};
