<?php

namespace Modules\Catalogue\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Catalogue\Models\CatalogueCategory;
use Modules\Catalogue\Models\CatalogueItem;
use Modules\Catalogue\Models\CatalogueProduct;
use Modules\Procurement\Models\PurchaseRequisition;

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
        if (!$product->is_active) {
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
            'delivery_point' => 'required|string|max:255',
        ]);

        $item = CatalogueItem::findOrFail($request->sku_id);

        $cart = session()->get('marketplace_cart', []);

        // Key by item ID + delivery point to allow different points for same item if needed,
        // or just keep it simple if it's per PR.
        // Flow says: Insert delivery point -> Add to cart. Usually per item or per cart?
        // Prompt says: 1. Choose items, 2. Insert Qty, 3. Insert delivery point, 4. Add to cart.
        // This implies per item.

        $cartKey = $item->id . '_' . Str::slug($request->delivery_point);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'sku_id' => $item->id,
                'quantity' => $request->quantity,
                'price' => $item->price,
                'name' => $item->product->name . ' (' . $item->sku . ')',
                'image' => $item->primaryImage->image_path ?? null,
                'delivery_point' => $request->delivery_point,
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
        $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $cart = session()->get('marketplace_cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        $user = auth()->user();
        $companyId = session('selected_company_id');

        // Generate PR Number
        $prNumber = 'PR-' . date('Y') . '-' . strtoupper(Str::random(6));

        $approvalStatus = 'pending'; // For marketplace, default to pending instead of draft
        $status = 'pending';
        $tenderStatus = 'closed';
        $submittedAt = now();

        // Create Purchase Requisition (B2B Tender)
        $pr = PurchaseRequisition::create([
            'pr_number' => $prNumber,
            'company_id' => $companyId,
            'user_id' => $user->id,
            'title' => $request->title ?: 'Tender Request - ' . now()->format('d M Y'),
            'description' => 'Tender request from Marketplace',
            'status' => $status,
            'approval_status' => $approvalStatus,
            'type' => 'tender',
            'tender_status' => $tenderStatus,
            'submitted_at' => $submittedAt,
            'delivery_point' => $cart[array_key_first($cart)]['delivery_point'] ?? null,
        ]);

        foreach ($cart as $id => $details) {
            $item = CatalogueItem::find($details['sku_id']);
            if ($item) {
                $pr->items()->create([
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                    'catalogue_item_id' => $item->id,
                    'delivery_point' => $details['delivery_point'] ?? null,
                ]);
            }
        }

        // Clear cart
        session()->forget('marketplace_cart');

        return redirect()->route('procurement.pr.show', $pr)
            ->with('success', 'Purchase Request created successfully.');
    }
}
