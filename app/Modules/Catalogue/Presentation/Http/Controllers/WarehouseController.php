<?php

namespace App\Modules\Catalogue\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $companyId = session('selected_company_id');

        // Simple Dashboard Stats
        $totalItems = CatalogueItem::where('company_id', $companyId)->count();
        $totalStock = CatalogueItem::where('company_id', $companyId)->sum('stock');
        $lowStockItems = CatalogueItem::where('company_id', $companyId)
            ->where('stock', '<', 10) // Threshold 10 for now
            ->limit(5)
            ->get();

        $recentItems = CatalogueItem::where('company_id', $companyId)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('warehouse.index', compact('totalItems', 'totalStock', 'lowStockItems', 'recentItems'));
    }

    public function scan()
    {
        return view('warehouse.scan');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $companyId = session('selected_company_id');
        $input = $request->qr_code;
        $sku = $input;

        // Try to decode JSON
        $json = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($json['sku'])) {
            $sku = $json['sku'];
        }

        // Try to find item by SKU
        $item = CatalogueItem::where('company_id', $companyId)
            ->where('sku', $sku)
            ->with(['product', 'images'])
            ->first();

        if (! $item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found.']);
        }

        return response()->json([
            'status' => 'success',
            'item' => [
                'name' => $item->product->name,
                'sku' => $item->sku,
                'stock' => $item->stock,
                'price' => number_format($item->price, 0),
                'unit' => $item->unit,
                'image' => $item->primary_image_url, // Accessor or logic needed
                'product_url' => route('catalogue.show', $item->product),
            ],
        ]);
    }

    public function generateQr($id)
    {
        $item = CatalogueItem::findOrFail($id);

        // Generate QR Code as SVG
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd
        );
        $writer = new Writer($renderer);

        $qrData = json_encode([
            'id' => $item->id,
            'sku' => $item->sku,
            'name' => $item->product->name ?? 'Unknown',
            'url' => route('catalogue.show', $item->catalogue_product_id),
        ]);

        $qrImage = $writer->writeString($qrData);

        return response($qrImage)->header('Content-Type', 'image/svg+xml');
    }

    public function adjustStock(Request $request)
    {
        $request->validate([
            'sku' => 'required|string',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
        ]);

        $companyId = session('selected_company_id');
        $item = CatalogueItem::where('company_id', $companyId)
            ->where('sku', $request->sku)
            ->first();

        if (! $item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found.']);
        }

        // Snapshot previous stock
        $prevStock = $item->stock;

        if ($request->type === 'in') {
            $item->increment('stock', $request->quantity);
        } else {
            if ($item->stock < $request->quantity) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient stock.']);
            }
            $item->decrement('stock', $request->quantity);
        }

        // Refresh to get new stock
        $item->refresh();

        // Log Movement
        \App\Modules\Catalogue\Domain\Models\StockMovement::create([
            'company_id' => $companyId,
            'catalogue_item_id' => $item->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'quantity' => $request->quantity,
            'previous_stock' => $prevStock,
            'current_stock' => $item->stock,
            'reference_type' => 'manual_scan',
            'notes' => 'Stock adjustment via Warehouse Scan',
        ]);

        return response()->json([
            'status' => 'success',
            'new_stock' => $item->stock,
            'message' => 'Stock updated successfully.',
        ]);
    }
}
