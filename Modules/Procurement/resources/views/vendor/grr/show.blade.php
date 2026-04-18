@extends('layouts.app', [
    'title' => 'Technical Claim Detail: ' . $goodsReturnRequest->grr_number,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'My Sales', 'url' => route('procurement.po.index')],
        ['name' => 'Return Claims', 'url' => route('procurement.grr.index')],
        ['name' => 'Claim Analysis', 'url' => null],
    ]
])

@section('content')
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">TECHNICAL CLAIM</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">SUBMITTED BY: {{ $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder->purchaseRequisition->company->name }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">After-Sales <span class="text-primary-600">Discrepancy</span></h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Claim Analysis --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Client Report Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 p-10 shadow-sm relative overflow-hidden">
                <div class="flex items-start justify-between mb-10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-3xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center text-3xl font-black">
                            <i data-feather="file-text"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Claimed Inventory</p>
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                {{ $goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? 'UNKNOWN ITEM' }}
                            </h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                FROM ORDER: {{ $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder->po_number }} • LODGED: {{ $goodsReturnRequest->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Claimed Qty</p>
                        <p class="text-4xl font-black text-red-600">{{ $goodsReturnRequest->quantity_affected }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Client's Issue Description</p>
                        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase leading-relaxed font-mono">
                            "{{ $goodsReturnRequest->issue_description ?? 'NO DETAILED DESCRIPTION PROVIDED.' }}"
                        </div>
                    </div>

                    @if($goodsReturnRequest->photo_evidence)
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Evidence Shared by Client</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($goodsReturnRequest->photo_evidence as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="block aspect-square rounded-2xl overflow-hidden border-2 border-transparent hover:border-indigo-500 transition-all">
                                        <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover" alt="Evidence">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Vendor Actions --}}
            @if($goodsReturnRequest->resolution_status === 'pending')
                <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-600/20 rounded-full blur-[80px]"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">CLAIM RESPONSE</span>
                        </div>
                        
                        <form action="{{ route('procurement.grr.vendor-response', $goodsReturnRequest) }}" method="POST">
                            @csrf
                            <div class="mb-8">
                                <label for="vendor_notes" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Technical Response / Findings</label>
                                <textarea name="vendor_notes" id="vendor_notes" rows="3" required
                                    class="w-full p-6 rounded-3xl bg-white/5 border border-white/10 text-sm font-bold placeholder-gray-600 focus:ring-indigo-500 transition-all"
                                    placeholder="Explain your decision or findings..."></textarea>
                            </div>

                            <div class="flex gap-4">
                                <button type="submit" name="action" value="approve"
                                    class="flex-1 py-4 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20">
                                    Approve Claim
                                </button>
                                <button type="submit" name="action" value="reject"
                                    class="flex-1 py-4 bg-white/10 text-white border border-white/10 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-white/20 transition-all">
                                    Reject Claim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Shipping Replacement Controls --}}
            @if($goodsReturnRequest->resolution_status === 'awaiting_replacement_shipping' || $goodsReturnRequest->resolution_status === 'approved_by_vendor' && $goodsReturnRequest->resolution_type === 'replacement')
                 <div class="bg-primary-600 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
                    <div class="relative z-10">
                        <h3 class="text-2xl font-black mb-8 uppercase tracking-tight">Ship Replacement Items</h3>
                        
                        <form action="{{ route('procurement.grr.ship-replacement', $goodsReturnRequest) }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[9px] font-black text-white/50 uppercase tracking-widest mb-2">Tracking Number</label>
                                    <input type="text" name="tracking_number" required
                                        class="w-full bg-white/10 border-white/20 rounded-xl text-[11px] font-black uppercase focus:ring-white focus:bg-white/20 transition-all p-4">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-white/50 uppercase tracking-widest mb-2">Expected Delivery</label>
                                    <input type="date" name="expected_delivery_date"
                                        class="w-full bg-white/10 border-white/20 rounded-xl text-[11px] font-black uppercase focus:ring-white focus:bg-white/20 transition-all p-4">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-4 bg-white text-primary-600 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                                Update Shipping Information
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- Process Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Workflow Status</h3>
                
                <div class="space-y-4">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl text-center border border-gray-100 dark:border-gray-800 shadow-inner">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Current State</p>
                        <p class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ str_replace('_', ' ', $goodsReturnRequest->resolution_status) }}</p>
                    </div>

                    <div class="p-6 bg-indigo-50 dark:bg-indigo-900/10 rounded-3xl border border-indigo-100 dark:border-indigo-800">
                        <p class="text-[9px] font-black text-indigo-900 dark:text-indigo-400 uppercase tracking-widest mb-1">Client Preference</p>
                        <p class="text-base font-black text-indigo-700 dark:text-indigo-300 uppercase tracking-tight">{{ $goodsReturnRequest->resolution_type_label ?? 'AWAITING SELECTION' }}</p>
                    </div>
                </div>
            </div>

            {{-- Case Metadata --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Engagement History</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <i data-feather="plus-circle" class="w-4 h-4 text-gray-300 mt-1"></i>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">Claim Received</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase">{{ $goodsReturnRequest->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @if($goodsReturnRequest->resolution_status !== 'pending')
                         <div class="flex items-start gap-4">
                            <i data-feather="check-circle" class="w-4 h-4 text-emerald-500 mt-1"></i>
                            <div>
                                <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">Internal Decision</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase">{{ $goodsReturnRequest->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endpush
