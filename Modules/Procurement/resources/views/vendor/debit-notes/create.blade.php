@extends('layouts.app', [
    'title' => 'Issue Debit Note: ' . $goodsReturnRequest->grr_number,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Returns', 'url' => route('procurement.grr.index')],
        ['name' => 'Issue Debit Note', 'url' => null],
    ]
])

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-1">
            <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">FINANCIAL ADJUSTMENT</span>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">CLAIM #{{ $goodsReturnRequest->grr_number }}</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-3">
            Generate <span class="text-primary-600">Debit Note</span>
        </h1>
        <p class="text-gray-500 font-medium lowercase">Issue a credit adjustment to settle the reported discrepancy for this claim.</p>
    </div>

    <form action="{{ route('procurement.debit-notes.store', $goodsReturnRequest) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left column --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Claim Context --}}
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Linked Claim Breakdown</h3>
                    <div class="flex items-start gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 shadow-inner">
                            <i data-feather="package" class="w-8 h-8"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight leading-tight mb-2">
                                {{ $goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? 'UNKNOWN ITEM' }}
                            </p>
                            <div class="flex items-center gap-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                <span>Ref: {{ $goodsReturnRequest->grr_number }}</span>
                                <span class="text-gray-200">|</span>
                                <span>Impacted Qty: {{ $goodsReturnRequest->quantity_affected }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Adjustment Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-8">Financial Adjustments</h3>
                    
                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label for="deduction_percentage" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Deduction Percentage (%)</label>
                                <div class="relative">
                                    <input type="number" name="deduction_percentage" id="deduction_percentage" 
                                        step="0.01" min="0" max="100"
                                        class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl p-4 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all"
                                        placeholder="e.g. 50">
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 font-black">%</div>
                                </div>
                            </div>
                            <div>
                                <label for="deduction_amount" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Or Fixed Deduction Amount</label>
                                <input type="number" name="deduction_amount" id="deduction_amount" 
                                    step="0.01" min="0"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl p-4 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all"
                                    placeholder="Enter fixed amount...">
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Adjustment Rationale</label>
                            <textarea name="reason" id="reason" rows="3" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl p-6 text-[11px] font-bold uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all font-mono"
                                placeholder="Explain the reason for this financial adjustment (e.g. Technical defect refund, partial credit)..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column - Overview --}}
            <div class="space-y-6">
                <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-12 -mt-12 w-32 h-32 bg-primary-600/20 rounded-full blur-[40px]"></div>
                    
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-8 relative z-10">Settlement Preview</h3>
                    
                    <div class="space-y-6 relative z-10">
                        <div class="flex justify-between items-center text-[10px] font-bold text-gray-500 uppercase tracking-widest border-b border-white/5 pb-4">
                            <span>Claim Value</span>
                            <span class="text-white">{{ number_format($originalAmount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] font-bold text-gray-500 uppercase tracking-widest border-b border-white/5 pb-4">
                            <span>Proposed Credit</span>
                            <span class="text-red-400 font-black" id="preview_deduction">0.00</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Final Total</span>
                            <span class="text-2xl font-black text-primary-400" id="preview_final">{{ number_format($originalAmount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Decision Authority</h3>
                    <p class="text-[10px] font-bold text-gray-500 uppercase leading-relaxed mb-8">
                        Upon issuance, the client's finance department must approve this adjustment for the final transaction value to be synchronized.
                    </p>
                    <button type="submit" class="w-full py-5 bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-3xl shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
                        Issue Debit Note
                    </button>
                    <a href="{{ route('procurement.grr.show', $goodsReturnRequest) }}" class="w-full py-4 mt-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-3xl flex items-center justify-center hover:bg-gray-100 transition-all">
                        Discard Draft
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        const originalAmount = {{ $originalAmount }};
        const percInput = document.getElementById('deduction_percentage');
        const amtInput = document.getElementById('deduction_amount');
        const previewDeduct = document.getElementById('preview_deduction');
        const previewFinal = document.getElementById('preview_final');

        function updatePreview() {
            let deduction = 0;
            if (percInput.value) {
                deduction = (originalAmount * parseFloat(percInput.value)) / 100;
                amtInput.value = ''; // Clear other input
            } else if (amtInput.value) {
                deduction = parseFloat(amtInput.value);
                percInput.value = ''; // Clear other input
            }

            const final = originalAmount - deduction;
            
            previewDeduct.textContent = deduction.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            previewFinal.textContent = final.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        percInput.addEventListener('input', updatePreview);
        amtInput.addEventListener('input', updatePreview);
    });
</script>
@endpush
