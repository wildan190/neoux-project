<?php

namespace App\Modules\Catalogue\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalogue\Domain\Models\CatalogueCategory;
use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use App\Modules\Catalogue\Domain\Models\CatalogueProduct;
use App\Modules\Procurement\Domain\Models\PurchaseRequisition;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('search') && $request->search) {
            // Use Laravel Scout
            $query = CatalogueProduct::search($request->search)
                ->query(function ($builder) use ($request) {
                    $builder->with(['category', 'items'])
                        ->where('is_active', true);

                    if ($request->has('category') && $request->category) {
                        $builder->where('category_id', $request->category);
                    }
                });
        } else {
            $query = CatalogueProduct::with(['category', 'items'])
                ->where('is_active', true);

            if ($request->has('category') && $request->category) {
                $query->where('category_id', $request->category);
            }
        }

        $products = $query->paginate(12);
        $categories = CatalogueCategory::all();

        return view('procurement.marketplace.index', compact('products', 'categories'));
    }

    public function show(CatalogueProduct $product)
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->load(['category', 'items.images', 'items.attributes']);

        return view('procurement.marketplace.show', compact('product'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'sku_id' => 'required|exists:catalogue_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CatalogueItem::findOrFail($request->sku_id);

        $cart = session()->get('marketplace_cart', []);

        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity'] += $request->quantity;
        } else {
            $cart[$item->id] = [
                'sku_id' => $item->id,
                'quantity' => $request->quantity,
                'price' => $item->price,
                'name' => $item->product->name.' ('.$item->sku.')',
                'image' => $item->primaryImage->image_path ?? null,
            ];
        }

        session()->put('marketplace_cart', $cart);

        return redirect()->back()->with('success', 'Item added to cart');
    }

    public function viewCart()
    {
        $cart = session()->get('marketplace_cart', []);

        return view('procurement.marketplace.cart', compact('cart'));
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('marketplace_cart', []);
        if (isset($cart[$request->sku_id])) {
            unset($cart[$request->sku_id]);
            session()->put('marketplace_cart', $cart);
        }

        return redirect()->back()->with('success', 'Item removed from cart');
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('marketplace_cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        $user = auth()->user();
        $companyId = session('selected_company_id');

        // Create Purchase Requisition (Direct Purchase)
        $pr = PurchaseRequisition::create([
            'id' => Str::uuid(),
            'company_id' => $companyId,
            'user_id' => $user->id,
            'title' => 'Direct Purchase - '.now()->format('d M Y'),
            'description' => 'Direct purchase from Marketplace',
            'status' => 'pending',
            'type' => 'direct', // direct purchase / non-tender
            'tender_status' => 'closed', // Since it's direct
        ]);

        foreach ($cart as $id => $details) {
            $item = CatalogueItem::find($id);
            if ($item) {
                // Here we might need a PurchaseRequisitionItem model that supports linking to CatalogueItem directly
                // Start with creating simple items.
                // Assuming PR Items table structure.
                // We'll insert it as a pending item.

                // Note: Current PurchaseRequisitionItem might separate from CatalogueItem?
                // Let's assume we copy details.
                // Or if we have a direct link.

                $pr->items()->create([
                    // 'purchase_requisition_id' handled by relation
                    'quantity' => $details['quantity'],
                    'price' => $details['price'], // Fixed: Mapped to 'price' column matches DB and Model
                    'catalogue_item_id' => $item->id,
                ]);
            }
        }

        // Clear cart
        session()->forget('marketplace_cart');

        return redirect()->route('procurement.pr.show', $pr)
            ->with('success', 'Purchase Request created successfully.');
    }
}
