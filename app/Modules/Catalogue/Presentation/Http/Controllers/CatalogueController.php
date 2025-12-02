<?php

namespace App\Modules\Catalogue\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalogue\Domain\Models\CatalogueCategory;
use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use App\Modules\Catalogue\Presentation\Http\Requests\StoreCatalogueItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Modules\Company\Domain\Models\Company; // Added this import

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('selected_company_id');

        if (!$companyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        // Check if company is approved
        $company = Company::find($companyId);
        if (!$company || !in_array($company->status, ['approved', 'active'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Catalogue access is only available for approved companies. Current status: ' . ($company->status ?? 'unknown'));
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
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->latest()->paginate(12);
        $categories = CatalogueCategory::all();

        return view('catalogue.index', compact('items', 'categories'));
    }

    public function create()
    {
        $companyId = session('selected_company_id');

        if (!$companyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        // Check if company is approved
        $company = Company::find($companyId);
        if (!$company || !in_array($company->status, ['approved', 'active'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Catalogue access is only available for approved companies. Current status: ' . ($company->status ?? 'unknown'));
        }

        $categories = CatalogueCategory::all();
        $generatedSku = CatalogueItem::generateSKU($companyId);

        return view('catalogue.create', compact('categories', 'generatedSku'));
    }

    public function store(StoreCatalogueItemRequest $request)
    {
        $companyId = session('selected_company_id');

        if (!$companyId) {
            return redirect()->route('dashboard')->with('error', 'Please select a company first.');
        }

        // Check if company is approved/active
        $company = Company::find($companyId);
        if (!$company || !in_array($company->status, ['approved', 'active'])) {
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
                if (!empty($attribute['key']) && !empty($attribute['value'])) {
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
                if (!empty($attribute['key']) && !empty($attribute['value'])) {
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
}
