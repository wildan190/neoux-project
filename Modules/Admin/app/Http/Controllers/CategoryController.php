<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalogue\Models\CatalogueCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = CatalogueCategory::withCount('items')->orderBy('name')->get();

        return view('admin::categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalogue_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('categories', 'public');
            $validated['icon'] = $path;
        }

        CatalogueCategory::create($validated);

        return redirect()->back()->with('success', 'Taxonomy node successfully deployed into the marketplace architecture.');
    }

    public function update(Request $request, CatalogueCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalogue_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $path = $request->file('icon')->store('categories', 'public');
            $validated['icon'] = $path;
        }

        $category->update($validated);

        return redirect()->back()->with('success', 'Taxonomy node successfully recalibrated.');
    }

    public function destroy(CatalogueCategory $category)
    {
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }
        $category->delete();

        return redirect()->back()->with('success', 'Taxonomy node successfully purged from core architecture.');
    }
}
