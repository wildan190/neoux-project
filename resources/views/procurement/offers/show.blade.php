@extends('layouts.app', [
    'title' => 'Offer Details',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => $purchaseRequisition->title, 'url' => route('procurement.offers.index', $purchaseRequisition)],
        ['name' => 'Offer Details', 'url' => null],
    ]
])

@section('content')
    {{-- Offer Header --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border-2 
            @if($offer->is_recommended) border-primary-300 dark:border-primary-700
            @elseif($offer->status === 'accepted') border-green-300 dark:border-green-700
            @else border-gray-100 dark:border-gray-700 @endif mb-6">

        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-14 h-14 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-xl font-bold text-primary-700 dark:text-primary-400">
                            #{{ $offer->rank_position }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $offer->company->name }}</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Submitted by {{ $offer->user->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        @if($offer->is_recommended)
                            <span
                                class="px-3 py-1.5 bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 text-sm font-bold rounded-full inline-flex items-center gap-1">
                                <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                Recommended
                            </span>
                        @endif

                        @if($offer->status === 'accepted')
                            <span
                                class="px-3 py-1.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-sm font-bold rounded-full inline-flex items-center gap-1">
                                <i data-feather="award" class="w-4 h-4"></i>
                                Winner
                            </span>
                        @elseif($offer->status === 'winning')
                            <span
                                class="px-3 py-1.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 text-sm font-bold rounded-full inline-flex items-center gap-1">
                                <i data-feather="clock" class="w-4 h-4"></i>
                                Winning (Pending Approval)
                            </span>
                        @elseif($offer->status === 'negotiating')
                            <span
                                class="px-3 py-1.5 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-sm font-bold rounded-full inline-flex items-center gap-1">
                                <i data-feather="message-circle" class="w-4 h-4"></i>
                                Negotiating
                            </span>
                        @elseif($offer->status === 'rejected')
                            <span
                                class="px-3 py-1.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-sm font-bold rounded-full">
                                Rejected
                            </span>
                        @else
                            <span
                                class="px-3 py-1.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-sm font-bold rounded-full">
                                Pending
                            </span>
                        @endif

                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Score: <strong>{{ $offer->rank_score }}/100</strong>
                        </span>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $offer->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Total Offer</p>
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}
                    </p>
                </div>
            </div>

            @if($offer->notes)
                <div class="mt-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Proposal Notes</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $offer->notes }}</p>
                </div>
            @endif

            {{-- Bidding Details --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Delivery Time</p>
                    <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-semibold">
                        <i data-feather="truck" class="w-4 h-4 text-primary-500"></i>
                        {{ $offer->delivery_time ?? 'N/A' }}
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Warranty</p>
                    <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-semibold">
                        <i data-feather="shield" class="w-4 h-4 text-primary-500"></i>
                        {{ $offer->warranty ?? 'N/A' }}
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Payment Scheme</p>
                    <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white font-semibold">
                        <i data-feather="credit-card" class="w-4 h-4 text-primary-500"></i>
                        {{ $offer->payment_scheme ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div
        class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-6">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Offered Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Product</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Requested</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Offered</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Unit Price</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($offer->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item->purchaseRequisitionItem->catalogueItem->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">SKU:
                                    {{ $item->purchaseRequisitionItem->catalogueItem->sku }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $item->purchaseRequisitionItem->quantity }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold 
                                            @if($item->quantity_offered >= $item->purchaseRequisitionItem->quantity) text-green-600 dark:text-green-400
                                            @else text-yellow-600 dark:text-yellow-400 @endif">
                                    {{ $item->quantity_offered }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $item->formatted_unit_price }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                {{ $item->formatted_subtotal }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <td colspan="4"
                            class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Total
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-primary-600 dark:text-primary-400">
                            {{ $offer->formatted_total_price }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Supporting Documents --}}
    @if($offer->documents->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-6">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">
                    <i data-feather="paperclip" class="w-5 h-5 inline mr-2"></i>
                    Supporting Documents
                </h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                @foreach($offer->documents as $doc)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-primary-300 dark:hover:border-primary-600 transition group">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                <i data-feather="file-text" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $doc->file_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $doc->formatted_file_size }} • {{ strtoupper(pathinfo($doc->file_name, PATHINFO_EXTENSION)) }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" 
                           target="_blank"
                           download
                           class="flex-shrink-0 px-4 py-2 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-400 text-sm font-semibold rounded-lg transition inline-flex items-center gap-2">
                            <i data-feather="download" class="w-4 h-4"></i>
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    {{-- Actions --}}
    @php
        $isCompanyManager = Auth::user()->companies()->where('companies.id', $purchaseRequisition->company_id)->wherePivotIn('role', ['owner', 'admin'])->exists();
        $isHeadApprover = Auth::id() === $purchaseRequisition->head_approver_id || Auth::user()->is_admin || $isCompanyManager;
        
        // Vendor check: Offer Creator OR Company Owner OR Company Member
        $isVendor = Auth::id() === $offer->user_id || Auth::id() === $offer->company->user_id || $offer->company->members->contains(Auth::id());

        $showActions = ($isCompanyManager && in_array($offer->status, ['pending', 'negotiating'])) || 
                      ($isHeadApprover && $offer->status === 'winning') ||
                      ($isVendor && $offer->status === 'negotiating');
        
        // Ensure headApprover is available for name display
        if($offer->status === 'winning' && !$purchaseRequisition->relationLoaded('headApprover')) {
            $purchaseRequisition->load('headApprover');
        }
    @endphp

    @if($showActions)
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                        @if($offer->status === 'winning') Approve Winner @else Review This Offer @endif
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($offer->status === 'winning') 
                             <span class="text-indigo-600 font-bold">Action Required:</span> Waiting for <strong>{{ $purchaseRequisition->headApprover->name ?? 'Head Approver' }}</strong> to award this tender.
                        @elseif($offer->status === 'negotiating')
                             @if($isVendor)
                                <span class="text-indigo-600 font-bold">Action Required:</span> Buyer has proposed new terms. Please accept or reject them.
                             @else
                                 <span class="text-blue-600 font-bold">Status:</span> 
                                 @if($offer->negotiation_message)
                                    Waiting for <strong>Vendor</strong> to approve revised terms.
                                 @else
                                    Waiting for <strong>{{ $offer->company->name }}</strong> to revise their offer.
                                 @endif
                             @endif
                        @elseif($offer->status === 'pending')
                             <span class="text-gray-600 font-bold">Status:</span> Review their offer below. You can invite them to negotiate or select them as a winner.
                        @else
                            Negotiate with this vendor, reject them, or select them as the potential winner.
                        @endif
                    </p>
                </div>

                <div class="flex gap-3">
                    @if($isCompanyManager && in_array($offer->status, ['pending', 'negotiating']))
                        @if($offer->status === 'pending')
                            <button onclick="document.getElementById('negotiateModal').classList.remove('hidden')"
                                class="px-6 py-3 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-sm font-bold rounded-lg transition">
                                <i data-feather="edit-3" class="w-4 h-4 inline mr-1"></i>
                                Propose Negotiation
                            </button>
                        @endif

                        <form action="{{ route('procurement.offers.reject', $offer) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Reject this offer?')"
                                class="px-6 py-3 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-bold rounded-lg transition">
                                <i data-feather="x" class="w-4 h-4 inline mr-1"></i>
                                Reject Offer
                            </button>
                        </form>

                        <form action="{{ route('procurement.offers.accept', $offer) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Select this offer as the potential winner? This will require final approval from the Purchasing Manager/Head.')"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                                <i data-feather="check-circle" class="w-4 h-4 inline mr-1"></i>
                                Select as Winner
                            </button>
                        </form>
                    @endif

                    @if($isHeadApprover && $offer->status === 'winning')
                        <form action="{{ route('procurement.offers.approve-winner', $offer) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Give final approval to award this tender to {{ $offer->company->name }}?')"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                                <i data-feather="check-square" class="w-4 h-4 inline mr-1"></i>
                                Approve Winner
                            </button>
                        </form>

                        {{-- Reject back to pending or just reject? For now reject the offer entirely --}}
                        <form action="{{ route('procurement.offers.reject', $offer) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Reject this winning selection?')"
                                class="px-6 py-3 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-bold rounded-lg transition">
                                <i data-feather="x" class="w-4 h-4 inline mr-1"></i>
                                Reject Winner
                            </button>
                        </form>
                        </form>
                    @endif

                     @if($offer->status === 'negotiating' && $isVendor)
                        <div class="flex gap-2">
                             <form action="{{ route('procurement.offers.vendor-reject-negotiation', $offer) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Reject the proposed terms? This acts as a rejection of the negotiation.')"
                                    class="px-6 py-3 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-bold rounded-lg transition">
                                    <i data-feather="x-circle" class="w-4 h-4 inline mr-1"></i>
                                    Reject Terms
                                </button>
                            </form>
                            <form action="{{ route('procurement.offers.vendor-accept-negotiation', $offer) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Accept the updated terms?')"
                                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                                    <i data-feather="check" class="w-4 h-4 inline mr-1"></i>
                                    Accept Updated Terms
                                </button>
                            </form>
                        </div>
                     @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Generate PO Action --}}
    @if($isOwner && $offer->status === 'accepted')
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Next Steps</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($purchaseRequisition->po_generated_at)
                            Purchase Order has been generated for this offer.
                        @else
                            Generate a Purchase Order to officially order these items from the vendor.
                        @endif
                    </p>
                </div>

                @if($purchaseRequisition->po_generated_at)
                    <a href="{{ route('procurement.po.show', $purchaseRequisition->purchaseOrder) }}" 
                       class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-lg transition shadow-sm inline-flex items-center gap-2">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        View Purchase Order
                    </a>
                @else
                    <form action="{{ route('procurement.po.generate', $purchaseRequisition) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Generate Purchase Order? This will create a formal PO document.')"
                                class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-lg transition shadow-sm inline-flex items-center gap-2">
                            <i data-feather="file-plus" class="w-4 h-4"></i>
                            Generate Purchase Order
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ $isOwner ? route('procurement.pr.show', $purchaseRequisition) : route('procurement.offers.my') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            {{ $isOwner ? 'Back to Request' : 'Back to My Offers' }}
        </a>
    </div>

    {{-- Negotiate Modal (Proposal Form) --}}
    <div id="negotiateModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 relative h-auto max-h-[90vh] overflow-y-auto">
            <button onclick="document.getElementById('negotiateModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Propose Negotiation (Update Terms)</h3>
            <p class="text-sm text-gray-500 mb-4">Modify the offer terms below to propose a negotiation. The vendor will be notified to accept or reject these new terms.</p>
            
            <form action="{{ route('procurement.offers.submit-negotiation', $offer) }}" method="POST">
                @csrf
                <div class="space-y-4">
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Negotiation Message (Optional)</label>
                        <textarea name="negotiation_message" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Reason for change..."></textarea>
                    </div>
                
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Price (Rp)</label>
                        <input type="number" name="total_price" value="{{ $offer->total_price }}" step="0.01" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delivery Time</label>
                            <input type="text" name="delivery_time" value="{{ $offer->delivery_time }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Warranty</label>
                            <input type="text" name="warranty" value="{{ $offer->warranty }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Scheme</label>
                        <input type="text" name="payment_scheme" value="{{ $offer->payment_scheme }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ $offer->notes }}</textarea>
                    </div>
                    <div class="flex gap-3 pt-2">
                         <button type="button" onclick="document.getElementById('negotiateModal').classList.add('hidden')" class="flex-1 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">Cancel</button>
                         <button type="submit" class="flex-1 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold">Send Proposal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush