<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MarketController;
use Modules\Catalogue\Models\CatalogueItem;
use Modules\Catalogue\Models\CatalogueCategory;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    $search = $request->input('search');
    $categoryFilter = $request->input('category');

    $categories = CatalogueCategory::all();

    $query = CatalogueItem::with(['product', 'primaryImage', 'company'])->where('is_active', true);

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

    if ($categoryFilter) {
        $query->whereHas('product', function($q) use ($categoryFilter) {
            $q->where('category_id', CatalogueCategory::where('slug', $categoryFilter)->value('id'));
        });
    }

    $featuredProducts = $query->latest()->paginate(16);

    return view('welcome', compact('featuredProducts', 'categories', 'search', 'categoryFilter'));
});

Route::get('/market', [MarketController::class, 'index'])->name('market.index');
Route::get('/market/{id}', [MarketController::class, 'show'])->name('market.show');

// Main Dashboard is now in User Module
// Route::get('/dashboard', [\Modules\User\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
// This is actually handled by the User module's web.php now.
