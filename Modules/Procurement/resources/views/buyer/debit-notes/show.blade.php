@extends('layouts.app', [
    'title' => 'Debit Note Analysis: DN-' . $debitNote->id,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Returns', 'url' => route('procurement.grr.index')],
        ['name' => 'Debit Note', 'url' => null],
    ]
])

@section('content')
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">FINANCIAL ADJUSTMENT</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">REF: DN-{{ $debitNote->id }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Purchase <span class="text-primary-600">Debit Note</span></h1>
        </div>
        
        <div class="flex items-center gap-3">
             <a href="{{ route('procurement.debit-notes.print', $debitNote) }}" target="_blank"
                class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition-all inline-flex items-center gap-2">
                <i data-feather="printer" class="w-4 h-4"></i>
                Print Document
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Analysis Area --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Adjustment Summary --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 p-10 shadow-sm relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-3xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center text-3xl font-black shadow-inner">
                            <i data-feather="dollar-sign"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Settlement Value</p>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                {{ number_format($debitNote->adjusted_amount, 2) }}
                            </h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic">
                                Original: {{ number_format($debitNote->original_amount, 2) }} (-{{ number_format($debitNote->deduction_amount, 2) }})
                            </p>
                        </div>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Deduction %</p>
                        <p class="text-4xl font-black text-red-600">{{ $debitNote->deduction_percentage }}%</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-10 border-t border-gray-50 dark:border-gray-700/50">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Context (GRR Reference)</p>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">CASE: {{ $debitNote->goodsReturnRequest->grr_number }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">ISSUE: {{ $debitNote->goodsReturnRequest->issue_type_label }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Authority (Purchase Order)</p>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">PO: {{ $debitNote->purchaseOrder->po_number }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">VENDOR: {{ $debitNote->purchaseOrder->vendorCompany->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Vendor's Adjustment Rationale</p>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase leading-relaxed font-mono">
                        "{{ $debitNote->reason ?? 'NO DETAILED RATIONALE PROVIDED.' }}"
                    </div>
                </div>
            </div>

            {{-- Audit Controls --}}
            @if($debitNote->status === 'pending' && $isBuyer)
                <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary-600/20 rounded-full blur-[80px]"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">FINANCE APPROVAL</span>
                        </div>
                        
                        <h3 class="text-2xl font-black mb-1 leading-tight uppercase tracking-tight">Validate Settlement</h3>
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-10">Accepting this adjustment will resolve the associated return claim.</p>
                        
                        <div class="flex flex-col md:flex-row gap-4">
                            <form action="{{ route('procurement.debit-notes.approve', $debitNote) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" onclick="return confirm('Approve this debit note and finalize the adjustment?')"
                                    class="w-full py-5 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-3xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
                                    Approve & Synchronize
                                </button>
                            </form>
                            <form action="{{ route('procurement.debit-notes.reject', $debitNote) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" onclick="return confirm('Reject this adjustment proposal?')"
                                    class="w-full py-5 bg-white/5 border border-white/10 text-white text-[11px] font-black uppercase tracking-widest rounded-3xl hover:bg-white/10 transition-all active:scale-95">
                                    Decline Proposal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Note Lifecycle</h3>
                
                <div class="space-y-4">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl text-center border border-gray-100 dark:border-gray-800 shadow-inner">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Authorization State</p>
                        <p class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $debitNote->status }}</p>
                    </div>

                    @if($debitNote->status === 'approved')
                        <div class="p-6 bg-emerald-50 dark:bg-emerald-900/10 rounded-3xl border border-emerald-100 dark:border-emerald-800 flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-emerald-600/20">
                                <i data-feather="check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-emerald-900 dark:text-emerald-400 uppercase tracking-widest">Validated At</p>
                                <p class="text-[11px] font-black text-emerald-800 dark:text-emerald-300">{{ $debitNote->approved_by_vendor_at ? $debitNote->approved_by_vendor_at->format('M d, Y') : $debitNote->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Involved Parties</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Customer / Buyer</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $debitNote->purchaseOrder->purchaseRequisition->company->name }}</p>
                    </div>
                    <div class="pt-6 border-t border-gray-50 dark:border-gray-700/50">
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Source / Vendor</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $debitNote->purchaseOrder->vendorCompany->name }}</p>
                    </div>
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
