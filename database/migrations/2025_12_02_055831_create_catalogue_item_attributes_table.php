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
        Schema::create('catalogue_item_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalogue_item_id');
            $table->string('attribute_key'); // e.g., "Size", "Color", "Material"
            $table->string('attribute_value'); // e.g., "XL", "Red", "Cotton"
            $table->timestamps();

            $table->foreign('catalogue_item_id')->references('id')->on('catalogue_items')->onDelete('cascade');

            $table->index('catalogue_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogue_item_attributes');
    }
};
