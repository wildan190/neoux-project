<?php

namespace App\Modules\Catalogue\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalogue\Domain\Models\CatalogueCategory;
use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use App\Modules\Catalogue\Presentation\Http\Requests\StoreCatalogueItemRequest;
use App\Modules\Company\Domain\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel; // Added this import

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('selected_company_id');

        if (! $companyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        // Check if company is approved
        $company = Company::find($companyId);
        if (! $company || ! in_array($company->status, ['approved', 'active'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Catalogue access is only available for approved companies. Current status: '.($company->status ?? 'unknown'));
        }

        $query = CatalogueItem::where('company_id', $companyId)
            ->with(['category', 'primaryImage']);

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Search by name or SKU
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('sku', 'like', '%'.$request->search.'%');
            });
        }

        $items = $query->latest()->paginate(12);
        $categories = CatalogueCategory::all();

        return view('catalogue.index', compact('items', 'categories'));
    }

    public function create()
    {
        $companyId = session('selected_company_id');

        if (! $companyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        // Check if company is approved
        $company = Company::find($companyId);
        if (! $company || ! in_array($company->status, ['approved', 'active'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Catalogue access is only available for approved companies. Current status: '.($company->status ?? 'unknown'));
        }

        $categories = CatalogueCategory::all();
        $generatedSku = CatalogueItem::generateSKU($companyId);

        return view('catalogue.create', compact('categories', 'generatedSku'));
    }

    public function store(StoreCatalogueItemRequest $request)
    {
        $companyId = session('selected_company_id');

        if (! $companyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        // Check if company is approved/active
        $company = Company::find($companyId);
        if (! $company || ! in_array($company->status, ['approved', 'active'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Catalogue access is only available for approved companies.');
        }

        $data = $request->validated();
        $data['company_id'] = $companyId;

        // Create catalogue item
        $item = CatalogueItem::create($data);

        // Handle attributes
        if ($request->has('attributes') && is_array($request->input('attributes'))) {
            foreach ($request->input('attributes') as $attribute) {
                if (! empty($attribute['key']) && ! empty($attribute['value'])) {
                    $item->attributes()->create([
                        'attribute_key' => $attribute['key'],
                        'attribute_value' => $attribute['value'],
                    ]);
                }
            }
        }

        // Handle images
        if ($request->hasFile('images')) {
            $primaryIndex = $request->input('primary_image_index', 0);

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('catalogue_images', 'public');
                $item->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index == $primaryIndex,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('catalogue.index')
            ->with('success', 'Catalogue item created successfully.');
    }

    public function show(CatalogueItem $item)
    {
        // Check ownership
        if ($item->company_id !== session('selected_company_id')) {
            abort(403, 'Unauthorized action.');
        }

        $item->load(['category', 'attributes', 'images']);

        return view('catalogue.show', compact('item'));
    }

    public function edit(CatalogueItem $item)
    {
        // Check ownership
        if ($item->company_id !== session('selected_company_id')) {
            abort(403, 'Unauthorized action.');
        }

        $categories = CatalogueCategory::all();
        $item->load(['attributes', 'images']);

        return view('catalogue.edit', compact('item', 'categories'));
    }

    public function update(StoreCatalogueItemRequest $request, CatalogueItem $item)
    {
        // Check ownership
        if ($item->company_id !== session('selected_company_id')) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();
        $item->update($data);

        // Update attributes - delete old and create new
        $item->attributes()->delete();
        if ($request->has('attributes') && is_array($request->input('attributes'))) {
            foreach ($request->input('attributes') as $attribute) {
                if (! empty($attribute['key']) && ! empty($attribute['value'])) {
                    $item->attributes()->create([
                        'attribute_key' => $attribute['key'],
                        'attribute_value' => $attribute['value'],
                    ]);
                }
            }
        }

        // Handle deleted images
        if ($request->has('deleted_images') && is_array($request->deleted_images)) {
            foreach ($request->deleted_images as $imageId) {
                $image = $item->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        // Handle new images if uploaded
        if ($request->hasFile('images')) {
            $existingImagesCount = $item->images()->count();
            $primaryIndex = $request->input('primary_image_index', $existingImagesCount);

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('catalogue_images', 'public');
                $item->images()->create([
                    'image_path' => $path,
                    'is_primary' => ($existingImagesCount + $index) == $primaryIndex,
                    'order' => $existingImagesCount + $index,
                ]);
            }
        }

        return redirect()->route('catalogue.show', $item)
            ->with('success', 'Catalogue item updated successfully.');
    }

    public function destroy(CatalogueItem $item)
    {
        // Check ownership
        if ($item->company_id !== session('selected_company_id')) {
            abort(403, 'Unauthorized action.');
        }

        // Delete images from storage
        foreach ($item->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $item->delete();

        return redirect()->route('catalogue.index')
            ->with('success', 'Catalogue item deleted successfully.');
    }

    /**
     * Generate SKU via AJAX
     */
    public function generateSku(Request $request)
    {
        $companyId = session('selected_company_id');
        $categoryId = $request->input('category_id');

        $sku = CatalogueItem::generateSKU($companyId, $categoryId);

        return response()->json(['sku' => $sku]);
    }

    /**
     * Handle Mass Import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        $companyId = session('selected_company_id');
        $file = $request->file('file');

        // Store file temporarily
        $path = $file->store('temp_imports');

        // Create import job record
        $importJob = \App\Modules\Catalogue\Domain\Models\ImportJob::create([
            'user_id' => auth()->id(),
            'company_id' => $companyId,
            'type' => 'catalogue',
            'status' => 'pending',
            'file_name' => $file->getClientOriginalName(),
            'total_rows' => 0, // Will be updated during import
        ]);

        // Dispatch Job
        \App\Modules\Catalogue\Application\Jobs\ImportCatalogueItemsJob::dispatch($path, $companyId, $importJob->id);

        return redirect()->back()->with([
            'success' => 'Import process started in background. You will be notified when completed.',
            'import_job_id' => $importJob->id,
        ]);
    }

    /**
     * Download Import Template
     */
    public function downloadTemplate()
    {
        // Create a simple CSV/Excel template
        $headers = ['sku', 'name', 'description', 'category', 'tags', 'attributes'];
        $example = ['SKU-001', 'Example Product', 'Description here', 'Electronics', 'tag1,tag2', 'Color:Red,Size:XL'];

        $callback = function () use ($headers, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=catalogue_import_template.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Preview Import Data (AJAX)
     */
    public function previewImport(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            ]);

            $file = $request->file('file');
            $path = $file->store('temp_previews');

            // Create a simple importer to extract headers and data
            $previewData = [];
            $headers = [];

            $collection = Excel::toCollection(new class implements \Maatwebsite\Excel\Concerns\ToCollection, \Maatwebsite\Excel\Concerns\WithHeadingRow
            {
                public function collection(\Illuminate\Support\Collection $rows)
                {
                    // This method is required but we won't use it here
                }
            }, $path)->first(); // Get first sheet

            if ($collection->isNotEmpty()) {
                // Get headers from first row keys (convert to array first)
                $firstRow = $collection->first();
                $headers = is_array($firstRow) ? array_keys($firstRow) : array_keys($firstRow->toArray());
                // Get preview data (first 20 rows)
                $previewData = $collection->take(20)->toArray();
            }

            // Clean up temp file
            \Storage::delete($path);

            return response()->json([
                'success' => true,
                'headers' => $headers,
                'data' => $previewData,
                'total_rows' => count($previewData),
                'file_name' => $file->getClientOriginalName(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid file. Please upload Excel or CSV file (max 10MB).',
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Preview import error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to read file: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check Import Status (for AJAX polling)
     */
    public function checkImportStatus(Request $request)
    {
        $importJobId = $request->input('import_job_id');
        $importJob = \App\Modules\Catalogue\Domain\Models\ImportJob::find($importJobId);

        if (! $importJob) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Import job not found',
            ], 404);
        }

        return response()->json([
            'status' => $importJob->status,
            'progress' => $importJob->progress_percentage,
            'processed_rows' => $importJob->processed_rows,
            'total_rows' => $importJob->total_rows,
            'file_name' => $importJob->file_name,
            'error_message' => $importJob->error_message,
        ]);
    }

    /**
     * Bulk Delete Catalogue Items
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:catalogue_items,id',
        ]);

        $companyId = session('selected_company_id');

        try {
            $deleted = CatalogueItem::where('company_id', $companyId)
                ->whereIn('id', $request->ids)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deleted} item".($deleted > 1 ? 's' : ''),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete items: '.$e->getMessage(),
            ], 500);
        }
    }
}
