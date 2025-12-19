<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $items = DB::table('catalogue_items')
            ->whereNull('catalogue_product_id')
            ->get();

        foreach ($items as $item) {
            // Create a Product for this Item
            $productId = DB::table('catalogue_products')->insertGetId([
                // 'id' => Str::uuid(), // REMOVED: Auto-increment
                'company_id' => $item->company_id,
                'category_id' => $item->category_id,
                'name' => $item->name,
                'slug' => Str::slug($item->name).'-'.Str::random(6),
                'brand' => null,
                'description' => $item->description,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update the Item to link to the new Product
            DB::table('catalogue_items')
                ->where('id', $item->id)
                ->update([
                    'catalogue_product_id' => $productId,
                    'is_active' => true,
                    // If price/stock were null, set defaults
                    'price' => 0,
                    'stock' => 0,
                    'unit' => 'Pcs',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: We can revert by setting catalogue_product_id to null
        // and deleting products created during this migration.
        // But for safe-keeping, simpler to do nothing or manual cleanup.
    }
};
