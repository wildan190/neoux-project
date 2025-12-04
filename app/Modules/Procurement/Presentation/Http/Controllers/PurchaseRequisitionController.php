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
    public function index()
    {
        $requisitions = PurchaseRequisition::with(['user.userDetail', 'items'])
            ->where('company_id', Auth::user()->companies->first()->id) // Assuming user belongs to one company for now or context is set
            ->latest()
            ->paginate(10);

        return view('procurement.pr.index', compact('requisitions'));
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
        ]);

        DB::transaction(function () use ($request) {
            $companyId = Auth::user()->companies->first()->id;

            $requisition = PurchaseRequisition::create([
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

        return redirect()->route('procurement.pr.my-requests')->with('success', 'Purchase Requisition created successfully.');
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

    public function publicFeed()
    {
        $requisitions = PurchaseRequisition::with(['user.userDetail', 'company', 'items', 'comments'])
            ->latest()
            ->paginate(12);

        return view('procurement.pr.public-feed', compact('requisitions'));
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
