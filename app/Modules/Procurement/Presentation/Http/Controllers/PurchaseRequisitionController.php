<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use App\Modules\Procurement\Domain\Models\PurchaseRequisition;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionItem;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionDocument;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $selectedCompanyId = session('selected_company_id');
        $filter = $request->get('filter', 'all'); // Default to 'all' for owned PRs

        $query = PurchaseRequisition::with(['user.userDetail', 'items'])
            ->where('company_id', $selectedCompanyId);

        if ($filter === 'open') {
            $query->where('status', 'pending');
        } elseif ($filter === 'closed') {
            $query->whereIn('status', ['awarded', 'ordered']);
        }
        // 'all' shows everything for this company

        $requisitions = $query->latest()->paginate(10)->appends(['filter' => $filter]);

        // Counts for badges
        $openCount = PurchaseRequisition::where('company_id', $selectedCompanyId)
            ->where('status', 'pending')->count();
        $closedCount = PurchaseRequisition::where('company_id', $selectedCompanyId)
            ->whereIn('status', ['awarded', 'ordered'])->count();

        return view('procurement.pr.index', compact('requisitions', 'filter', 'openCount', 'closedCount'));
    }

    public function myRequests()
    {
        $requisitions = PurchaseRequisition::with(['items', 'company'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('procurement.pr.my-requests', compact('requisitions'));
    }

    public function create()
    {
        // Fetch catalogue items for the dropdown
        // Assuming we need to fetch items for the user's company
        $companyId = Auth::user()->companies->first()->id;
        $catalogueItems = CatalogueItem::where('company_id', $companyId)->get();

        return view('procurement.pr.create', compact('catalogueItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.catalogue_item_id' => 'required|exists:catalogue_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'documents.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ], [
            'title.required' => 'Request title is required.',
            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item must be added.',
            'items.*.catalogue_item_id.required' => 'Please select an item.',
            'items.*.catalogue_item_id.exists' => 'Selected item does not exist.',
            'items.*.quantity.required' => 'Quantity is required.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.price.required' => 'Price is required.',
            'items.*.price.min' => 'Price cannot be negative.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $companyId = session('selected_company_id');

                if (!$companyId) {
                    $companyId = Auth::user()->companies->first()?->id;
                }

                if (!$companyId) {
                    throw new \Exception('No company found for this user.');
                }

                // Generate PR Number
                $prNumber = 'PR-' . date('Y') . '-' . strtoupper(Str::random(6));

                $requisition = PurchaseRequisition::create([
                    'pr_number' => $prNumber,
                    'company_id' => $companyId,
                    'user_id' => Auth::id(),
                    'title' => $request->title,
                    'description' => $request->description,
                    'status' => 'pending',
                ]);

                foreach ($request->items as $item) {
                    PurchaseRequisitionItem::create([
                        'purchase_requisition_id' => $requisition->id,
                        'catalogue_item_id' => $item['catalogue_item_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                // Handle document uploads
                if ($request->hasFile('documents')) {
                    foreach ($request->file('documents') as $file) {
                        $originalName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $filename = Str::uuid() . '.' . $extension;
                        $path = $file->storeAs('procurement/documents/' . $requisition->id, $filename, 'public');

                        PurchaseRequisitionDocument::create([
                            'purchase_requisition_id' => $requisition->id,
                            'original_name' => $originalName,
                            'file_path' => $path,
                            'file_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                            'uploaded_by' => Auth::id(),
                        ]);
                    }
                }
            });

            return redirect()->route('procurement.pr.my-requests')->with('success', 'Purchase Requisition created successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create Purchase Requisition: ' . $e->getMessage());
        }
    }

    public function show(PurchaseRequisition $purchaseRequisition)
    {
        // Ensure user belongs to the same company
        if ($purchaseRequisition->company_id !== Auth::user()->companies->first()->id) {
            abort(403);
        }

        $purchaseRequisition->load(['company', 'user.userDetail', 'items.catalogueItem', 'documents.uploader', 'comments.user.userDetail']);

        return view('procurement.pr.show', compact('purchaseRequisition'));
    }

    public function showPublic(PurchaseRequisition $purchaseRequisition)
    {
        // No authorization check - public view for all users
        $purchaseRequisition->load(['company', 'user.userDetail', 'items.catalogueItem', 'documents.uploader', 'comments.user.userDetail']);

        return view('procurement.pr.show-public', compact('purchaseRequisition'));
    }

    public function publicFeed(Request $request)
    {
        $filter = $request->get('filter', 'open'); // Default to 'open'

        $query = PurchaseRequisition::with(['user.userDetail', 'company', 'items', 'comments']);

        if ($filter === 'open') {
            // Open = pending status, no winner yet
            $query->where('status', 'pending');
        } elseif ($filter === 'closed') {
            // Closed = awarded or ordered (has winner)
            $query->whereIn('status', ['awarded', 'ordered']);
        }
        // 'all' shows everything

        $requisitions = $query->latest()->paginate(12)->appends(['filter' => $filter]);

        // Count for badges
        $openCount = PurchaseRequisition::where('status', 'pending')->count();
        $closedCount = PurchaseRequisition::whereIn('status', ['awarded', 'ordered'])->count();

        return view('procurement.pr.public-feed', compact('requisitions', 'filter', 'openCount', 'closedCount'));
    }

    public function downloadDocument(PurchaseRequisitionDocument $document)
    {
        // Optional: Add authorization check here
        // e.g., if ($document->purchaseRequisition->company_id !== Auth::user()->companies->first()->id) abort(403);

        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $document->original_name);
    }

    public function addComment(Request $request, PurchaseRequisition $purchaseRequisition)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:purchase_requisition_comments,id',
        ]);

        PurchaseRequisitionComment::create([
            'purchase_requisition_id' => $purchaseRequisition->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Comment posted successfully.');
    }
}
