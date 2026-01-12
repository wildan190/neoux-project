<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use App\Modules\Procurement\Domain\Models\PurchaseRequisition;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionComment;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionDocument;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionItem;
use App\Notifications\NewCommentAdded;
use App\Notifications\PurchaseOrderReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $companyId = session('selected_company_id');
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
                    $companyId = Auth::user()->allCompanies()->first()?->id;
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
        // Ensure we are in the context of the correct company (Session check)
        if ($purchaseRequisition->company_id != session('selected_company_id')) {
            abort(403, 'Unauthorized access to this requisition.');
        }

        $purchaseRequisition->load(['company.members', 'user.userDetail', 'items.catalogueItem', 'documents.uploader', 'comments.user.userDetail', 'offers.company']);

        return view('procurement.pr.show', compact('purchaseRequisition'));
    }

    public function showPublic(PurchaseRequisition $purchaseRequisition)
    {
        // No authorization check - public view for all users
        $purchaseRequisition->load(['company', 'user.userDetail', 'items.catalogueItem', 'documents.uploader', 'comments.user.userDetail']);

        $myOffer = null;
        $selectedCompanyId = session('selected_company_id');
        if (Auth::check() && $selectedCompanyId) {
            $myOffer = $purchaseRequisition->offers()
                ->where('company_id', $selectedCompanyId)
                ->with('items')
                ->first();
        }

        return view('procurement.pr.show-public', compact('purchaseRequisition', 'myOffer'));
    }

    public function publicFeed(Request $request)
    {
        $selectedCompanyId = session('selected_company_id');
        $filter = $request->get('filter', 'open');
        $search = $request->get('search');

        // Logic: If search exists, use Scout. Else use Eloquent.
        if ($search) {
            // Scout Search - Public Feed shows ALL requests
            $query = PurchaseRequisition::search($search)
                ->where('approval_status', 'approved'); // Only show Approved PRs in public feed

            // Apply status filter
            if ($filter === 'open') {
                $query->where('status', 'open');
            } elseif ($filter === 'closed') {
                // Scout 'where' is strict equality.
                // Ideally we use a dedicated index field 'is_closed' or similar.
                // But for database driver, we can use the callback to filter on the query builder.
                $query->query(function ($builder) {
                    $builder->whereIn('status', ['awarded', 'ordered']);
                });
            }

            // Eager load relationships for Scout results
            $query->query(function ($builder) {
                $builder->with(['user.userDetail', 'company', 'items']);
            });

            $requisitions = $query->paginate(10);

        } else {
            // Standard Eloquent (No Search) - Public Feed shows ALL requests
            $query = PurchaseRequisition::with(['user.userDetail', 'company', 'items'])
                ->where('approval_status', 'approved'); // Only show Approved PRs

            if ($filter === 'open') {
                $query->where('status', 'open');
            } elseif ($filter === 'closed') {
                $query->whereIn('status', ['awarded', 'ordered']);
            }

            $requisitions = $query->latest()->paginate(10);
        }

        // Append query params
        $requisitions->appends(['filter' => $filter, 'search' => $search]);

        // Counts for badges - Global counts for public feed
        $openCount = PurchaseRequisition::where('status', 'open')->count();
        $closedCount = PurchaseRequisition::whereIn('status', ['awarded', 'ordered'])->count();

        return view('procurement.pr.public-feed', compact('requisitions', 'filter', 'openCount', 'closedCount', 'search'));
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

        $comment = PurchaseRequisitionComment::create([
            'purchase_requisition_id' => $purchaseRequisition->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
        ]);

        if ($comment && $purchaseRequisition->user_id !== Auth::id()) {
            $purchaseRequisition->user->notify(new NewCommentAdded($comment));
        }

        return back()->with('success', 'Comment posted successfully.');
    }

    /**
     * Submit PR for Approval
     */
    public function submitForApproval(Request $request, PurchaseRequisition $purchaseRequisition)
    {
        $request->validate([
            'approver_id' => 'required|exists:users,id',
            'head_approver_id' => 'required|exists:users,id',
        ]);

        // Ensure user has permission (is owner or has admin/manager role)
        $userRole = Auth::user()->companies->find($purchaseRequisition->company_id)?->pivot->role ?? 'staff';
        $isAdminOrManager = in_array($userRole, ['admin', 'manager']);

        if ($purchaseRequisition->user_id !== Auth::id() && !$isAdminOrManager) {
            abort(403, 'Unauthorized to submit this requisition.');
        }

        $purchaseRequisition->update([
            'approver_id' => $request->approver_id,
            'head_approver_id' => $request->head_approver_id,
            'submitted_at' => now(),
            'approval_status' => 'pending_supervisor', // Step 1
        ]);

        // Auto-approve if single user company
        $memberCount = $purchaseRequisition->company->members()->count();
        if ($memberCount <= 1 || $request->approver_id == Auth::id() && $request->head_approver_id == Auth::id()) {
            // If the user assigned themselves (or no other members), auto-advance to approved if they are owner/admin
            $isAdmin = Auth::user()->companies->find($purchaseRequisition->company_id)?->pivot->role === 'admin';
            $isOwner = $purchaseRequisition->company->user_id === Auth::id();

            if ($isAdmin || $isOwner) {
                $purchaseRequisition->update([
                    'approval_status' => 'approved',
                    'status' => 'open',
                    'tender_status' => 'open'
                ]);
                return back()->with('success', 'Purchase Requisition submitted and auto-approved.');
            }
        }

        // TODO: Send notification to supervisor

        return back()->with('success', 'Purchase Requisition submitted for Supervisor approval.');
    }

    /**
     * Approve PR
     */
    public function approve(Request $request, PurchaseRequisition $purchaseRequisition)
    {
        $isAdmin = Auth::user()->companies->find($purchaseRequisition->company_id)?->pivot->role === 'admin';
        $isOwner = $purchaseRequisition->company->user_id === Auth::id();

        // Universal Bypass: Owner/Admin can approve anything
        if (!$isAdmin && !$isOwner) {
            if ($purchaseRequisition->approval_status === 'pending_supervisor' && $purchaseRequisition->approver_id !== Auth::id()) {
                abort(403, 'Only assigned supervisor can approve this step.');
            }
            if ($purchaseRequisition->approval_status === 'pending_head' && $purchaseRequisition->head_approver_id !== Auth::id()) {
                abort(403, 'Only assigned Head can approve this step.');
            }
        }

        if ($purchaseRequisition->approval_status === 'pending_supervisor') {
            $purchaseRequisition->update([
                'approval_status' => 'pending_head',
                'approval_notes' => $request->approval_notes,
            ]);

            // TODO: Notify Head
            return back()->with('success', 'Supervisor approval completed. Now pending Head approval.');
        }

        if ($purchaseRequisition->approval_status === 'pending_head') {
            $purchaseRequisition->update([
                'approval_status' => 'approved',
                'approval_notes' => $request->approval_notes,
                'status' => 'open', // Becomes active Tender
                'tender_status' => 'open',
            ]);

            // Notify all potential vendors (users in other companies)
            if ($purchaseRequisition->type === 'tender') {
                $vendors = \App\Modules\User\Domain\Models\User::where('id', '!=', Auth::id())->get();
                foreach ($vendors as $vendor) {
                    $vendor->notify(new \App\Notifications\TenderPublished($purchaseRequisition));
                }
            }

            // HANDLE DIRECT PURCHASE: Auto-generate PO(s)
            if ($purchaseRequisition->type === 'direct') {
                // Group items by Vendor (Catalogue Item's Company)
                $itemsByVendor = $purchaseRequisition->items->groupBy(function ($item) {
                    return $item->catalogueItem->company_id;
                });

                foreach ($itemsByVendor as $vendorId => $items) {
                    // Generate PO for this Vendor
                    $poNumber = 'PO-' . date('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

                    $totalAmount = $items->sum(function ($item) {
                        return $item->quantity * $item->price;
                    });

                    $po = \App\Modules\Procurement\Domain\Models\PurchaseOrder::create([
                        'po_number' => $poNumber,
                        'purchase_requisition_id' => $purchaseRequisition->id,
                        'vendor_company_id' => $vendorId,
                        'created_by_user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'total_amount' => $totalAmount,
                        'status' => 'issued', // Immediately issued
                    ]);

                    foreach ($items as $item) {
                        \App\Modules\Procurement\Domain\Models\PurchaseOrderItem::create([
                            'purchase_order_id' => $po->id,
                            'purchase_requisition_item_id' => $item->id,
                            'quantity_ordered' => $item->quantity,
                            'quantity_received' => 0,
                            'unit_price' => $item->price,
                            'subtotal' => $item->quantity * $item->price,
                        ]);

                        // Deduct Stock from Vendor (Seller)
                        $catalogueItem = $item->catalogueItem;
                        if ($catalogueItem) {
                            $catalogueItem->decrement('stock', $item->quantity);
                        }
                    }

                    // Notify Vendor Company Owner
                    $vendorCompany = \App\Modules\Company\Domain\Models\Company::find($vendorId);
                    if ($vendorCompany && $vendorCompany->user) {
                        $vendorCompany->user->notify(new PurchaseOrderReceived($po));
                    }

                    // Send Email Notification to Vendor
                    try {
                        // 1. Try Company Email
                        $company = \App\Modules\Company\Domain\Models\Company::find($vendorId);
                        $email = $company->email;

                        // 2. Fallback to Owner Email
                        if (!$email && $company->user) {
                            $email = $company->user->email;
                        }

                        if ($email) {
                            \Illuminate\Support\Facades\Mail::to($email)
                                ->send(new \App\Mail\PurchaseOrderSent($po));
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send PO email to vendor: ' . $e->getMessage());
                    }
                }

                // Update PR status to ordered
                $purchaseRequisition->update([
                    'status' => 'ordered',
                    'po_generated_at' => now(),
                ]);
            }

            return back()->with('success', 'Request approved successfully.');
        }

        return back()->with('error', 'Invalid approval state.');
    }

    /**
     * Reject PR
     */
    public function reject(Request $request, PurchaseRequisition $purchaseRequisition)
    {
        $isApprover = $purchaseRequisition->approver_id === Auth::id();
        $isAdmin = Auth::user()->companies->find($purchaseRequisition->company_id)?->pivot->role === 'admin';

        if (!$isApprover && !$isAdmin) {
            abort(403, 'Unauthorized');
        }

        $purchaseRequisition->update([
            'approval_status' => 'rejected',
            'approval_notes' => $request->approval_notes,
            'status' => 'draft', // Send back to draft?
        ]);

        return back()->with('success', 'Request rejected.');
    }

    /**
     * Assign PR to Staff
     */
    public function assign(Request $request, PurchaseRequisition $purchaseRequisition)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Only Admin/Manager can assign
        // Simplification for now: check if user belongs to company with role
        // For now, allow any member to assign (collaboration) or restrict to admin/manager

        $purchaseRequisition->update([
            'assigned_to' => $request->assigned_to,
        ]);

        return back()->with('success', 'Assigned successfully.');
    }
}
