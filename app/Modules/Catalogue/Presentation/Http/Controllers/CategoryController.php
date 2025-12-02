<?php

namespace App\Modules\Catalogue\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalogue\Domain\Models\CatalogueCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = CatalogueCategory::withCount('items')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:catalogue_categories,name',
            'description' => 'nullable|string',
        ]);

        CatalogueCategory::create($request->only(['name', 'description']));

        return back()->with('success', 'Category created successfully.');
    }

    public function destroy(CatalogueCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }
}
