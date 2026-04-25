@extends('layouts.app', [
    'title' => 'Receive Goods: ' . $purchaseOrder->po_number,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Logistic', 'url' => route('procurement.gr.index')],
        ['name' => 'Receive Goods', 'url' => null],
    ]
])

@section('content')
<div class="max-w-[1400px] mx-auto">
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-1">
            <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">INTAKE OPERATION</span>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">ORDER #{{ $purchaseOrder->po_number }}</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-3">
            Execute <span class="text-primary-600">Goods Receipt</span>
        </h1>
        <p class="text-gray-500 font-medium lowercase">Verify incoming quantities and technical condition against the procurement order.</p>
    </div>

    @if($isReplacement)
        <div class="mb-8 p-6 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-3xl flex items-center gap-6 shadow-sm">
            <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-600/20">
                <i data-feather="refresh-cw" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-[11px] font-black text-indigo-900 dark:text-indigo-200 uppercase tracking-widest leading-none mb-1">Replacement Mode Active</h4>
                <p class="text-[10px] font-bold text-indigo-700 dark:text-indigo-300 uppercase leading-relaxed">
                    You are receiving replacements for previously rejected or return-item shipments.
                </p>
            </div>
        </div>
    @endif

    <form action="{{ route('procurement.gr.store', $purchaseOrder) }}" method="POST">
        @csrf
        @if($isReplacement)
            <input type="hidden" name="is_replacement" value="1">
        @endif
        @if($deliveryOrder)
            <input type="hidden" name="delivery_order_id" value="{{ $deliveryOrder->id }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm group hover:border-primary-200 transition-all">
                <label for="received_at" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Reception Timestamp</label>
                <input type="date" name="received_at" id="received_at" required
                       class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black uppercase tracking-tight focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-gray-900 dark:text-white px-5 py-4">
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm group hover:border-primary-200 transition-all">
                <label for="warehouse_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Storage Point (Warehouse)</label>
                <div class="relative">
                    <select name="warehouse_id" id="warehouse_id" required
                            class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black uppercase tracking-tight focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-gray-900 dark:text-white appearance-none pl-5 pr-12 py-4 cursor-pointer">
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none">
                        <i data-feather="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm group hover:border-primary-200 transition-all">
                <label for="delivery_note" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Delivery Note (SJ) Number</label>
                <input type="text" name="delivery_note" id="delivery_note" 
                       placeholder="e.g. SJ-001"
                       class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black uppercase tracking-tight focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all placeholder-gray-400 dark:placeholder-gray-500 text-gray-900 dark:text-white px-5 py-4"
                       value="{{ $deliveryOrder->delivery_number ?? '' }}">
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-8">
            <div class="px-10 py-8 border-b border-gray-50 dark:border-gray-800/50 flex justify-between items-center">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Quantity & Quality Audit</h3>
                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-900 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-widest">
                    {{ $purchaseOrder->items->count() }} line items
                </span>
            </div>
            
            <div class="p-4 md:p-10 space-y-12">
                @foreach($purchaseOrder->items as $index => $item)
                    @php
                        $remaining = $item->quantity_ordered - ($item->quantity_received ?? 0);
                        $doItem = $deliveryOrder ? $deliveryOrder->items->where('item_id', $item->purchase_requisition_item_id)->first() : null;
                        
                        // For replacement, we might have specific items coming back
                        $maxPossible = $isReplacement ? 1000 : $remaining; // Simplicity for now
                    @endphp
                    
                    @if($remaining > 0 || $isReplacement)
                        <div class="relative group">
                            <div class="flex flex-col md:flex-row gap-8">
                                <div class="flex-1">
                                    <p class="text-[9px] font-black text-primary-600 uppercase tracking-widest mb-1">Item Details</p>
                                    <h4 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight leading-tight mb-2">
                                        {{ $item->purchaseRequisitionItem->catalogueItem->name ?? $item->item_name }}
                                    </h4>
                                    <div class="flex items-center gap-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        <span>Ordered: {{ $item->quantity_ordered }}</span>
                                        <span class="text-gray-300">|</span>
                                        <span>Remaining: {{ $remaining }}</span>
                                        @if($deliveryOrder && $doItem)
                                            <span class="text-gray-300">|</span>
                                            <span class="text-indigo-600">IN DO: {{ $doItem->quantity }}</span>
                                        @endif
                                    </div>
                                    <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $item->id }}">
                                </div>

                                <div class="w-full md:w-auto grid grid-cols-3 gap-3">
                                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-2xl border border-transparent focus-within:border-primary-300 transition-all">
                                        <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Received</label>
                                        <input type="number" name="items[{{ $index }}][quantity_received]" 
                                               id="qty_recv_{{ $index }}"
                                               value="{{ $doItem->quantity ?? $remaining }}"
                                               class="w-full bg-transparent border-0 p-0 text-lg font-black text-gray-900 dark:text-white focus:ring-0"
                                               min="0" oninput="distributeQty({{ $index }})">
                                    </div>
                                    <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-4 rounded-2xl border border-transparent focus-within:border-emerald-300 transition-all">
                                        <label class="block text-[8px] font-black text-emerald-400 uppercase tracking-widest mb-2">Good/Passed</label>
                                        <input type="number" name="items[{{ $index }}][quantity_good]" 
                                               id="qty_good_{{ $index }}"
                                               value="{{ $doItem->quantity ?? $remaining }}"
                                               class="w-full bg-transparent border-0 p-0 text-lg font-black text-emerald-600 focus:ring-0"
                                               min="0" oninput="adjustRejected({{ $index }})">
                                    </div>
                                    <div class="bg-red-50/50 dark:bg-red-900/10 p-4 rounded-2xl border border-transparent focus-within:border-red-300 transition-all">
                                        <label class="block text-[8px] font-black text-red-300 uppercase tracking-widest mb-2">Rejected</label>
                                        <input type="number" name="items[{{ $index }}][quantity_rejected]" 
                                               id="qty_rej_{{ $index }}"
                                               value="0"
                                               class="w-full bg-transparent border-0 p-0 text-lg font-black text-red-600 focus:ring-0"
                                               min="0" oninput="adjustGood({{ $index }})">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div class="relative">
                                    <i data-feather="alert-circle" class="w-4 h-4 absolute left-4 top-4 text-gray-300 dark:text-gray-600"></i>
                                    <input type="text" name="items[{{ $index }}][rejected_reason]" 
                                           placeholder="REASON FOR REJECTION (IF ANY)..."
                                           class="w-full pl-12 pr-4 py-4 bg-gray-50/50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-bold uppercase tracking-widest text-gray-600 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                                </div>
                                <div class="relative">
                                    <i data-feather="info" class="w-4 h-4 absolute left-4 top-4 text-gray-300 dark:text-gray-600"></i>
                                    <input type="text" name="items[{{ $index }}][condition]" 
                                           placeholder="TECHNICAL CONDITION NOTES..."
                                           class="w-full pl-12 pr-4 py-4 bg-gray-50/50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-400 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            @if(!$loop->last)
                                <div class="mt-12 border-b border-gray-50 dark:border-gray-800/50"></div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            
            <div class="p-10 bg-gray-50 dark:bg-gray-900/10 border-t border-gray-50 dark:border-gray-800/50">
                <label for="notes" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Overall Reception Remarks</label>
                <textarea name="notes" id="notes" rows="4" 
                          class="w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-[11px] font-bold uppercase tracking-tight focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 p-6"
                          placeholder="Anything significant about this delivery..."></textarea>
            </div>
        </div>

        <div class="flex items-center justify-between gap-6">
            <a href="{{ route('procurement.po.show', $purchaseOrder) }}" class="h-16 px-10 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[11px] font-black text-gray-400 uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                Cancel Intake
            </a>
            <button type="submit" class="h-16 flex-1 bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
                Finalize Goods Receipt
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    function distributeQty(index) {
        const recv = parseInt(document.getElementById(`qty_recv_${index}`).value) || 0;
        document.getElementById(`qty_good_${index}`).value = recv;
        document.getElementById(`qty_rej_${index}`).value = 0;
    }

    function adjustRejected(index) {
        const total = parseInt(document.getElementById(`qty_recv_${index}`).value) || 0;
        const good = parseInt(document.getElementById(`qty_good_${index}`).value) || 0;
        
        if (good > total) {
            document.getElementById(`qty_recv_${index}`).value = good;
            document.getElementById(`qty_rej_${index}`).value = 0;
        } else {
            document.getElementById(`qty_rej_${index}`).value = total - good;
        }
    }

    function adjustGood(index) {
        const total = parseInt(document.getElementById(`qty_recv_${index}`).value) || 0;
        const rej = parseInt(document.getElementById(`qty_rej_${index}`).value) || 0;
        
        if (rej > total) {
            document.getElementById(`qty_recv_${index}`).value = rej;
            document.getElementById(`qty_good_${index}`).value = 0;
        } else {
            document.getElementById(`qty_good_${index}`).value = total - rej;
        }
    }
</script>
@endpush
