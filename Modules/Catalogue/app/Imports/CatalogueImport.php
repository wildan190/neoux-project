<?php

namespace Modules\Catalogue\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Catalogue\Models\CatalogueCategory;
use Modules\Catalogue\Models\CatalogueItem;
use Modules\Catalogue\Models\CatalogueProduct;
use Modules\Catalogue\Models\ImportJob;

class CatalogueImport implements ToCollection, WithHeadingRow
{
    protected $companyId;
    protected $importJobId;

    public function __construct($companyId, $importJobId)
    {
        $this->companyId = $companyId;
        $this->importJobId = $importJobId;
    }

    public function collection(Collection $rows)
    {
        $importJob = ImportJob::find($this->importJobId);
        $importJob->update(['total_rows' => $rows->count()]);

        $processed = 0;

        foreach ($rows as $row) {
            try {
                // Ensure required fields
                if (empty($row['name']) || empty($row['category']) || empty($row['price'])) {
                    continue;
                }

                // Find or Create Category
                $category = CatalogueCategory::firstOrCreate(
                    ['slug' => Str::slug($row['category'])],
                    ['name' => $row['category']]
                );

                // Find or Create Product
                $product = CatalogueProduct::firstOrCreate(
                    [
                        'company_id' => $this->companyId,
                        'name' => $row['name'],
                    ],
                    [
                        'category_id' => $category->id,
                        'slug' => Str::slug($row['name']) . '-' . Str::random(6),
                        'brand' => $row['brand'] ?? null,
                        'description' => $row['description'] ?? null,
                        'is_active' => true,
                    ]
                );

                // Create Item (SKU)
                $sku = $row['sku'] ?? 'SKU-' . strtoupper(Str::random(8));

                CatalogueItem::updateOrCreate(
                    [
                        'company_id' => $this->companyId,
                        'catalogue_product_id' => $product->id,
                        'sku' => $sku,
                    ],
                    [
                        'category_id' => $category->id,
                        'name' => $row['name'], // Default to product name if variant has no specific name
                        'price' => $row['price'],
                        'stock' => $row['stock'] ?? 0,
                        'unit' => $row['unit'] ?? 'pcs',
                        'is_active' => true,
                    ]
                );

                $processed++;
                // Update progress every 10 rows
                if ($processed % 10 === 0) {
                    $importJob->update(['processed_rows' => $processed]);
                }

            } catch (\Exception $e) {
                // Log error per row if needed, but for now continue
                \Log::error("Import Row Failed: " . $e->getMessage());
            }
        }

        $importJob->update(['processed_rows' => $processed]);
    }
}
