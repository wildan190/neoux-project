<?php

namespace Modules\Catalogue\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Catalogue\Http\Requests\StoreCatalogueProductRequest; // Ensure this exists
// Keep for backward compat if needed or alias
use Modules\Catalogue\Http\Requests\StoreCatalogueSkuRequest;
use Modules\Catalogue\Models\CatalogueCategory;
use Modules\Catalogue\Models\CatalogueItem;
use Modules\Catalogue\Models\CatalogueProduct;
use Modules\Company\Models\Company;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('selected_company_id');

        if (!$companyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        if (!auth()->user()->hasCompanyPermission($companyId, 'access catalogue')) {
            abort(403, 'Unauthorized to access catalogue.');
        }

        // Check company status
        $company = Company::find($companyId);
        if (!$company || !in_array($company->status, ['approved', 'active'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Catalogue access is only available for approved companies.');
        }

        $query = CatalogueProduct::where('company_id', $companyId)
            ->with(['category', 'items']); // Load items to show count or price range

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(12);
        $categories = CatalogueCategory::all();

        return view('catalogue.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = CatalogueCategory::all();

        return view('catalogue.create', compact('categories'));
    }

    public function store(StoreCatalogueProductRequest $request)
    {
        $companyId = session('selected_company_id');
        $data = $request->validated();

        // 1. Create Product
        $product = CatalogueProduct::create([
            'company_id' => $companyId,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::random(6),
            'brand' => $data['brand'],
            'description' => $data['description'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        // 2. Create Default SKU (Variant)
        $item = CatalogueItem::create([
            'company_id' => $companyId,
            'catalogue_product_id' => $product->id,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'sku' => $data['sku'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'unit' => $data['unit'],
            'is_active' => true,
        ]);

        // 3. Handle Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('catalogue_images', 'public');
                $item->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index == 0,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('catalogue.show', $product)
            ->with('success', 'Product and Default Variant created successfully.');
    }

    // Show Product and its SKUs
    public function show(CatalogueProduct $product)
    {
        if ($product->company_id !== session('selected_company_id')) {
            abort(403);
        }

        $product->load(['category', 'items.images', 'items.attributes']);

        return view('catalogue.show', compact('product'));
    }

    public function edit(CatalogueProduct $product)
    {
        if ($product->company_id !== session('selected_company_id')) {
            abort(403);
        }
        $categories = CatalogueCategory::all();

        return view('catalogue.edit', compact('product', 'categories'));
    }

    public function update(StoreCatalogueProductRequest $request, CatalogueProduct $product)
    {
        if ($product->company_id !== session('selected_company_id')) {
            abort(403);
        }

        // Note: Update usually only updates Product details.
        // SKUs are managed separately in 'show' view or dedicated edit routes.
        // We accept that for strict RESTfulness, but 'update' here corresponds to Product entity.

        $product->update($request->validated());

        return redirect()->route('catalogue.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(CatalogueProduct $product)
    {
        if ($product->company_id !== session('selected_company_id')) {
            abort(403);
        }

        // Items are cascaded deleted by FK, but images need cleanup
        foreach ($product->items as $item) {
            foreach ($item->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $product->delete();

        return redirect()->route('catalogue.index')
            ->with('success', 'Product deleted successfully.');
    }

    // --- SKU / Variant Management ---

    public function storeSku(StoreCatalogueSkuRequest $request, CatalogueProduct $product)
    {
        if ($product->company_id !== session('selected_company_id')) {
            abort(403);
        }

        $data = $request->validated();
        $data['company_id'] = $product->company_id;
        $data['catalogue_product_id'] = $product->id;
        // Populate legacy fields to satisfy constraints and maintain backward compatibility
        $data['name'] = $product->name;
        $data['category_id'] = $product->category_id;

        // Create Item
        $item = CatalogueItem::create($data);

        // Attributes
        if ($request->has('attributes') && is_array($request->input('attributes'))) {
            foreach ($request->input('attributes') as $attribute) {
                if (!empty($attribute['key']) && !empty($attribute['value'])) {
                    $item->attributes()->create([
                        'attribute_key' => $attribute['key'],
                        'attribute_value' => $attribute['value'],
                    ]);
                }
            }
        }

        // Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('catalogue_images', 'public');
                $item->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index == 0, // First one is primary by default or handle input
                    'order' => $index,
                ]);
            }
        }

        return redirect()->back()->with('success', 'SKU added successfully.');
    }

    public function destroySku(CatalogueItem $item)
    {
        if ($item->company_id !== session('selected_company_id')) {
            abort(403);
        }

        // Cleanup images
        foreach ($item->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $item->delete();

        return redirect()->back()->with('success', 'Variant deleted successfully.');
    }

    // Additional methods (import, etc) need update or removal of old dependencies.
    // For brevity, skipping import refactoring in this specific file write, will handle separately.
    public function generateSku(Request $request)
    {
        // Logic to generate SKU based on category or random
        // For now simple random or based on category slug
        $prefix = 'SKU';
        if ($request->category_id) {
            $category = CatalogueCategory::find($request->category_id);
            if ($category) {
                $prefix = strtoupper(substr($category->slug, 0, 3));
            }
        }

        $sku = $prefix . '-' . strtoupper(Str::random(6));
        // ex: ELE-AB12CD

        return response()->json(['sku' => $sku]);
    }

    public function downloadTemplate()
    {
        \Illuminate\Support\Facades\Log::info('Download template hit');
        try {
            return Excel::download(new \Modules\Catalogue\Exports\CatalogueTemplateExport, 'catalogue_template.xlsx');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Download template failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $companyId = session('selected_company_id');

        $path = $request->file('file')->store('imports', 'local');

        // Create Import Job Record
        $importJob = \Modules\Catalogue\Models\ImportJob::create([
            'company_id' => $companyId,
            'user_id' => auth()->id(),
            'filename' => $request->file('file')->getClientOriginalName(),
            'status' => 'pending',
            'total_rows' => 0,
            'processed_rows' => 0,
        ]);

        try {
            // Dispatch Job with relative path
            \Modules\Catalogue\Jobs\ImportCatalogueItemsJob::dispatch(
                $path,
                $companyId,
                $importJob->id
            );

            return response()->json([
                'message' => 'Import started',
                'job_id' => $importJob->id,
            ]);
        } catch (\Exception $e) {
            $importJob->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return response()->json(['message' => 'Import failed to start: ' . $e->getMessage()], 500);
        }
    }

    public function checkImportStatus($id)
    {
        $job = \Modules\Catalogue\Models\ImportJob::find($id);
        if (!$job) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json($job);
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            // Read first 5 rows to preview
            $rows = Excel::toArray(new \stdClass, $request->file('file'));

            // Assume first sheet
            $sheetData = $rows[0] ?? [];

            // Remove header row if exists (simple assumption: first row is header)
            // But for preview, showing header is fine or we can skip it.
            // Let's just return the top 5 rows including header for clarity
            $previewData = array_slice($sheetData, 0, 6);

            return response()->json([
                'status' => 'success',
                'preview' => $previewData,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
