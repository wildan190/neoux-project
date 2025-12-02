<?php

namespace App\Modules\Catalogue\Application\Imports;

use App\Modules\Catalogue\Domain\Models\CatalogueCategory;
use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CatalogueImport implements ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    protected $companyId;

    protected $importJob;

    public function __construct($companyId, $importJob = null)
    {
        $this->companyId = $companyId;
        $this->importJob = $importJob;
    }

    public function collection(Collection $rows)
    {
        // Update total rows count if import job exists (only on first chunk)
        if ($this->importJob && $this->importJob->total_rows == 0) {
            $this->importJob->update(['total_rows' => $rows->count()]);
        }

        DB::transaction(function () use ($rows) {
            $processedCount = $this->importJob ? $this->importJob->processed_rows : 0;

            foreach ($rows as $index => $row) {
                try {
                    // Find or create category
                    $categoryId = null;
                    if (! empty($row['category'])) {
                        $category = CatalogueCategory::firstOrCreate(
                            ['name' => $row['category']],
                            ['slug' => Str::slug($row['category'])]
                        );
                        $categoryId = $category->id;
                    }

                    // Create Item
                    $item = CatalogueItem::create([
                        'company_id' => $this->companyId,
                        'category_id' => $categoryId,
                        'sku' => $row['sku'],
                        'name' => $row['name'],
                        'description' => $row['description'] ?? null,
                        'tags' => $row['tags'] ?? null,
                    ]);

                    // Create default image for imported products
                    $item->images()->create([
                        'image_path' => 'assets/img/products/default-product.png',
                        'is_primary' => true,
                        'order' => 0,
                    ]);

                    // Parse Attributes: "Color:Red, Size:XL"
                    if (! empty($row['attributes'])) {
                        $pairs = explode(',', $row['attributes']);
                        foreach ($pairs as $pair) {
                            $parts = explode(':', $pair);
                            if (count($parts) == 2) {
                                $key = trim($parts[0]);
                                $value = trim($parts[1]);
                                if ($key && $value) {
                                    $item->attributes()->create([
                                        'attribute_key' => $key,
                                        'attribute_value' => $value,
                                    ]);
                                }
                            }
                        }
                    }

                    $processedCount++;

                    // Update progress every 10 rows (instead of every row)
                    if ($this->importJob && $processedCount % 10 == 0) {
                        $this->importJob->update(['processed_rows' => $processedCount]);
                    }
                } catch (\Exception $e) {
                    // Log error but continue processing
                    \Log::error('Import row error: '.$e->getMessage(), [
                        'row' => $row,
                        'company_id' => $this->companyId,
                    ]);

                    // Continue to next row
                    continue;
                }
            }

            // Final progress update for remaining rows
            if ($this->importJob) {
                $this->importJob->update(['processed_rows' => $processedCount]);
            }
        });
    }

    public function batchSize(): int
    {
        return 100; // Process 100 rows at a time
    }

    public function chunkSize(): int
    {
        return 100; // Read 100 rows per chunk
    }

    public function rules(): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (CatalogueItem::where('company_id', $this->companyId)->where('sku', $value)->exists()) {
                        $fail('The SKU '.$value.' has already been taken.');
                    }
                },
            ],
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
        ];
    }
}
