<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Catalogue\Models\CatalogueItem;

class MarketController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = CatalogueItem::with(['product', 'primaryImage', 'company'])
            ->where('is_active', true);

        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(sku) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(tags) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereHas('product', function($pq) use ($searchLower) {
                      $pq->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                         ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"]);
                  })
                  ->orWhereHas('company', function($cq) use ($searchLower) {
                      $cq->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                  });
            });
        }

        $products = $query->latest()->paginate(16);
        $categories = \Modules\Catalogue\Models\CatalogueCategory::all();

        return view('catalogue::market.index', compact('products', 'search', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = CatalogueItem::with([
            'product', 
            'images', 
            'company',
            'product.category',
            'attributes'
        ])->where('is_active', true)->findOrFail($id);

        // Fetch related products from the same company or category
        $relatedProducts = CatalogueItem::with(['product', 'primaryImage', 'company'])
            ->where('is_active', true)
            ->where('id', '!=', $id)
            ->where(function($q) use ($product) {
                $q->where('company_id', $product->company_id);
            })
            ->latest()
            ->take(4)
            ->get();

        return view('catalogue::market.show', compact('product', 'relatedProducts'));
    }
}
