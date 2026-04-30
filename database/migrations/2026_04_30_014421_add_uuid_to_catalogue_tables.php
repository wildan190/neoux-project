<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('catalogue_products', function (Blueprint $table) {
            if (!Schema::hasColumn('catalogue_products', 'uuid')) {
                $table->uuid('uuid')->after('id')->nullable()->unique();
            }
        });

        Schema::table('catalogue_items', function (Blueprint $table) {
            if (!Schema::hasColumn('catalogue_items', 'uuid')) {
                $table->uuid('uuid')->after('id')->nullable()->unique();
            }
        });

        // Populate existing records with UUIDs
        DB::table('catalogue_products')->whereNull('uuid')->get()->each(function ($record) {
            DB::table('catalogue_products')->where('id', $record->id)->update(['uuid' => (string) Str::uuid()]);
        });

        DB::table('catalogue_items')->whereNull('uuid')->get()->each(function ($record) {
            DB::table('catalogue_items')->where('id', $record->id)->update(['uuid' => (string) Str::uuid()]);
        });

        // Make UUIDs non-nullable after population
        Schema::table('catalogue_products', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });

        Schema::table('catalogue_items', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogue_products', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('catalogue_items', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
