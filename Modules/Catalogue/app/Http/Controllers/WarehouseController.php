<?php

namespace Modules\Catalogue\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Catalogue\Models\CatalogueItem;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $companyId = session('selected_company_id');
        $warehouses = \Modules\Company\Models\Warehouse::where('company_id', $companyId)->where('is_active', true)->get();
        return view('warehouse.scan', compact('warehouses'));
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'warehouse_id' => 'required|exists:warehouses,id',
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
            ->with(['product'])
            ->first();

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found.']);
        }

        // Get Stock for specific warehouse
        $warehouseStock = \Modules\Catalogue\Models\WarehouseStock::firstOrCreate([
            'warehouse_id' => $request->warehouse_id,
            'catalogue_item_id' => $item->id,
        ]);

        return response()->json([
            'status' => 'success',
            'item' => [
                'name' => $item->product->name,
                'sku' => $item->sku,
                'stock' => $warehouseStock->quantity, // Show Warehouse Stock
                'unit' => $item->unit,
                'price' => number_format($item->price, 0),
                'product_url' => route('catalogue.show', $item->product),
            ],
            'warehouse_name' => $warehouseStock->warehouse->name,
        ]);
    }


    public function generateQr($id)
    {
        $companyId = session('selected_company_id');
        $item = CatalogueItem::where('company_id', $companyId)->where('id', $id)->with('product')->firstOrFail();

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        // QR Content: JSON with SKU is standard for our scanner
        $qrData = json_encode([
            'sku' => $item->sku,
        ]);

        $qrCode = $writer->writeString($qrData);

        return view('warehouse.qr', compact('item', 'qrCode'));
    }

    public function adjustStock(Request $request)
    {
        $request->validate([
            'sku' => 'required|string',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
        ]);

        $companyId = session('selected_company_id');
        $item = CatalogueItem::where('company_id', $companyId)
            ->where('sku', $request->sku)
            ->first();

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found.']);
        }

        $warehouseStock = \Modules\Catalogue\Models\WarehouseStock::firstOrCreate([
            'warehouse_id' => $request->warehouse_id,
            'catalogue_item_id' => $item->id,
        ]);

        // Snapshot previous stock
        $prevStock = $warehouseStock->quantity;

        DB::beginTransaction();
        try {
            if ($request->type === 'in') {
                $warehouseStock->increment('quantity', $request->quantity);
            } else {
                if ($warehouseStock->quantity < $request->quantity) {
                    return response()->json(['status' => 'error', 'message' => 'Insufficient stock in this warehouse.']);
                }
                $warehouseStock->decrement('quantity', $request->quantity);
            }

            // Sync global stock
            $newGlobalStock = \Modules\Catalogue\Models\WarehouseStock::where('catalogue_item_id', $item->id)->sum('quantity');
            $item->update(['stock' => $newGlobalStock]);

            // Log Movement
            \Modules\Catalogue\Models\StockMovement::create([
                'company_id' => $companyId,
                'catalogue_item_id' => $item->id,
                'warehouse_id' => $request->warehouse_id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
                'previous_stock' => $prevStock,
                'current_stock' => $warehouseStock->quantity,
                'reference_type' => 'manual_scan',
                'notes' => 'Stock adjustment via Warehouse Scan',
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'new_stock' => $warehouseStock->quantity,
                'message' => 'Stock updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed to update stock.']);
        }
    }
}
