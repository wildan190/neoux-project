@extends('layouts.app', [
    'title' => 'Debit Note: DN-' . $debitNote->id,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'My Sales', 'url' => route('procurement.po.index')],
        ['name' => 'Debit Notes', 'url' => route('procurement.debit-notes.index')],
        ['name' => 'Note Detail', 'url' => null],
    ]
])

@section('content')
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">ISSUED ADJUSTMENT</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">REF: DN-{{ $debitNote->id }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Financial <span class="text-primary-600">Discrepancy Note</span></h1>
        </div>
        
        <div class="flex items-center gap-3">
             <a href="{{ route('procurement.debit-notes.print', $debitNote) }}" target="_blank"
                class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition-all inline-flex items-center gap-2">
                <i data-feather="printer" class="w-4 h-4"></i>
                Print Copy
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Adjustment Overview --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 p-10 shadow-sm relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-3xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center text-3xl font-black shadow-inner">
                            <i data-feather="file-text"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Proposed Settlement</p>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                {{ number_format($debitNote->adjusted_amount, 2) }}
                            </h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic">
                                Credit to Client: {{ number_format($debitNote->deduction_amount, 2) }}
                            </p>
                        </div>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Deduction %</p>
                        <p class="text-4xl font-black text-indigo-600">{{ $debitNote->deduction_percentage }}%</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-10 border-t border-gray-50 dark:border-gray-700/50">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Origin (Return Claim)</p>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">CASE: {{ $debitNote->goodsReturnRequest->grr_number }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">CLIENT REASON: {{ $debitNote->goodsReturnRequest->issue_type_label }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Linked Sales Order</p>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">PO: {{ $debitNote->purchaseOrder->po_number }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">CUSTOMER: {{ $debitNote->purchaseOrder->purchaseRequisition->company->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Reasoning Provided</p>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase leading-relaxed font-mono">
                        "{{ $debitNote->reason ?? 'NO DETAILED RATIONALE PROVIDED.' }}"
                    </div>
                </div>
            </div>
        </div>

        {{-- Process Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Adjustment Stage</h3>
                
                <div class="space-y-4">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl text-center border border-gray-100 dark:border-gray-800 shadow-inner">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Approval Context</p>
                        <p class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $debitNote->status }}</p>
                    </div>

                    @if($debitNote->status === 'pending')
                         <div class="p-6 bg-yellow-50 dark:bg-yellow-900/10 rounded-3xl border border-yellow-100 dark:border-yellow-800">
                            <p class="text-[9px] font-black text-yellow-900 dark:text-yellow-400 uppercase tracking-widest mb-2 text-center">Awaiting Customer</p>
                            <p class="text-[10px] font-bold text-yellow-700 dark:text-yellow-300 uppercase leading-relaxed text-center">This adjustment must be validated by the client's finance team.</p>
                        </div>
                    @endif
                    
                    @if($debitNote->status === 'approved')
                        <div class="p-6 bg-emerald-50 dark:bg-emerald-900/10 rounded-3xl border border-emerald-100 dark:border-emerald-800 flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-600 text-white rounded-xl flex items-center justify-center">
                                <i data-feather="check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-emerald-900 dark:text-emerald-400 uppercase tracking-widest">Client Approved</p>
                                <p class="text-[11px] font-black text-emerald-800 dark:text-emerald-300">{{ $debitNote->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Financial Trail</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Debit Note Issued</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $debitNote->created_at->format('M d, Y H:i') }}</p>
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
