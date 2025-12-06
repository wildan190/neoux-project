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
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable();
            $table->foreignUuid('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approval_status')->default('draft'); // 'draft', 'pending', 'approved', 'rejected'
            $table->text('approval_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $table->dropForeign(['approver_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['submitted_at', 'approver_id', 'assigned_to', 'approval_status', 'approval_notes']);
        });
    }
};
