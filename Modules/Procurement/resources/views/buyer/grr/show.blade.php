@extends('layouts.app', [
    'title' => 'GRR Detail: ' . $goodsReturnRequest->grr_number,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Returns', 'url' => route('procurement.grr.index')],
        ['name' => 'Case Detail', 'url' => null],
    ]
])

@section('content')
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">TECHNICAL AUDIT</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">CASE ID: {{ $goodsReturnRequest->grr_number }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Return Claims <span class="text-primary-600">Audit</span></h1>
        </div>
        
        <div class="flex items-center gap-3">
            @if($goodsReturnRequest->resolution_status === 'replacement_shipped' && $isBuyer)
                <form action="{{ route('procurement.grr.confirm-replacement-receipt', $goodsReturnRequest) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Confirm receipt of replacement items?')"
                        class="px-8 py-4 bg-emerald-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                        Confirm Replacement Arrival
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Incident Report --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Issue Overview Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 p-10 shadow-sm relative overflow-hidden">
                <div class="flex items-start justify-between mb-10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-3xl bg-red-50 dark:bg-red-900/20 text-red-600 flex items-center justify-center text-3xl font-black shadow-inner">
                            <i data-feather="alert-octagon"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Impacted Inventory</p>
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                {{ $goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? 'UNKNOWN ITEM' }}
                            </h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                FROM GR: {{ $goodsReturnRequest->goodsReceiptItem->goodsReceipt->gr_number }} • VENDOR: {{ $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder->vendorCompany->name }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Affected Qty</p>
                        <p class="text-4xl font-black text-gray-900 dark:text-white">{{ $goodsReturnRequest->quantity_affected }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Defect Description</p>
                        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase leading-relaxed font-mono">
                            "{{ $goodsReturnRequest->issue_description ?? 'NO DETAILED DESCRIPTION PROVIDED.' }}"
                        </div>
                    </div>

                    @if($goodsReturnRequest->photo_evidence)
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Photographic Evidence</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($goodsReturnRequest->photo_evidence as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="block aspect-square rounded-2xl overflow-hidden border-2 border-transparent hover:border-primary-500 transition-all">
                                        <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover" alt="Evidence">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Post-Audit Resolution (Buyer Controls) --}}
            @if(in_array($goodsReturnRequest->resolution_status, ['pending', 'approved_by_vendor', 'rejected_by_vendor']))
                <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary-600/20 rounded-full blur-[80px]"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">RESOLUTION MANAGEMENT</span>
                        </div>
                        
                        <h3 class="text-2xl font-black mb-8 uppercase tracking-tight">Select Settlement Strategy</h3>
                        
                        <form action="{{ route('procurement.grr.update-resolution', $goodsReturnRequest) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @csrf
                            <button type="submit" name="resolution_type" value="replacement" 
                                class="p-6 rounded-3xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all text-left flex flex-col items-center justify-center text-center
                                {{ $goodsReturnRequest->resolution_type === 'replacement' ? 'ring-4 ring-primary-500 bg-white/10' : '' }}">
                                <i data-feather="refresh-cw" class="w-8 h-8 mb-4 text-primary-400"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Replacement</span>
                                <span class="text-[8px] font-bold text-gray-500 mt-2 uppercase">Request new items</span>
                            </button>
                            
                            <button type="submit" name="resolution_type" value="price_adjustment" 
                                class="p-6 rounded-3xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all text-left flex flex-col items-center justify-center text-center
                                {{ $goodsReturnRequest->resolution_type === 'price_adjustment' ? 'ring-4 ring-primary-500 bg-white/10' : '' }}">
                                <i data-feather="dollar-sign" class="w-8 h-8 mb-4 text-yellow-400"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Adjustment</span>
                                <span class="text-[8px] font-bold text-gray-500 mt-2 uppercase">Debit note refund</span>
                            </button>
                            
                            <button type="submit" name="resolution_type" value="return_refund" 
                                class="p-6 rounded-3xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all text-left flex flex-col items-center justify-center text-center
                                {{ $goodsReturnRequest->resolution_type === 'return_refund' ? 'ring-4 ring-primary-500 bg-white/10' : '' }}">
                                <i data-feather="corner-up-left" class="w-8 h-8 mb-4 text-red-400"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Full Return</span>
                                <span class="text-[8px] font-bold text-gray-500 mt-2 uppercase">Cancel transaction</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- Audit Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Case Status</h3>
                
                <div class="space-y-4">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl text-center border border-gray-100 dark:border-gray-800 shadow-inner">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Stage</p>
                        <p class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ str_replace('_', ' ', $goodsReturnRequest->resolution_status) }}</p>
                    </div>

                    @if($goodsReturnRequest->vendor_notes)
                        <div class="p-6 bg-indigo-50 dark:bg-indigo-900/10 rounded-3xl border border-indigo-100 dark:border-indigo-800">
                            <p class="text-[9px] font-black text-indigo-900 dark:text-indigo-400 uppercase tracking-widest mb-3">Vendor Remarks</p>
                            <p class="text-[11px] font-bold text-indigo-700 dark:text-indigo-300 uppercase leading-relaxed">"{{ $goodsReturnRequest->vendor_notes }}"</p>
                        </div>
                    @endif

                    @if($isBuyer && $goodsReturnRequest->resolution_status === 'approved_by_vendor')
                        <form action="{{ route('procurement.grr.resolve', $goodsReturnRequest) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Manually mark this case as resolved?')"
                                class="w-full py-4 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                                Close Audit Case
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Technical Metadata --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Technical Audit Trail</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-1.5 h-1.5 rounded-full bg-primary-600 mt-2"></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">Case Initiated</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase">{{ $goodsReturnRequest->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @if($goodsReturnRequest->resolved_at)
                        <div class="flex items-start gap-4">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-600 mt-2"></div>
                            <div>
                                <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">Case Resolved</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase">{{ $goodsReturnRequest->resolved_at->format('M d, Y H:i') }}</p>
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
