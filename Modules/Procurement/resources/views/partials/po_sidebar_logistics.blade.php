{{-- Logistics Card --}}
<div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Logistics Overview</h3>
    <div class="space-y-6">
        <div class="bg-primary-50 dark:bg-primary-900/10 p-4 rounded-2xl border border-primary-100 dark:border-primary-800/50">
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-[10px] font-black text-primary-600 uppercase mb-1">Total Fulfilment</p>
                    <p class="text-2xl font-black text-primary-700 dark:text-primary-400">{{ round(($purchaseOrder->items->sum('quantity_received') / max(1, $purchaseOrder->items->sum('quantity_ordered'))) * 100) }}%</p>
                </div>
                <i data-feather="activity" class="w-6 h-6 text-primary-400 opacity-50"></i>
            </div>
            <div class="w-full bg-primary-200 dark:bg-primary-800 h-1 rounded-full mt-3 overflow-hidden">
                <div class="bg-primary-600 h-full rounded-full" style="width: {{ ($purchaseOrder->items->sum('quantity_received') / max(1, $purchaseOrder->items->sum('quantity_ordered'))) * 100 }}%"></div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 dark:bg-gray-900/20 rounded-2xl border border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Ordered</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums">{{ $purchaseOrder->items->sum('quantity_ordered') }}</p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-900/20 rounded-2xl border border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Accepted</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400 tabular-nums">{{ $purchaseOrder->items->sum('quantity_received') }}</p>
            </div>
        </div>

        @if($isBuyer && $purchaseOrder->status === 'issued' && $purchaseOrder->items->sum('quantity_received') < $purchaseOrder->items->sum('quantity_ordered'))
            @php
                $hasShippedDO = $purchaseOrder->deliveryOrders()->where('status', 'shipped')->exists();
            @endphp
            
            @if($hasShippedDO)
                <a href="{{ route('procurement.gr.create', $purchaseOrder) }}" 
                   class="w-full py-4 bg-white dark:bg-gray-800 border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white transition flex items-center justify-center gap-2 rounded-2xl font-black text-sm shadow-xl shadow-primary-500/5">
                    <i data-feather="plus-circle" class="w-4 h-4"></i>
                    Log Receipt
                </a>
            @else
                <button disabled class="w-full py-4 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-gray-400 cursor-not-allowed flex items-center justify-center gap-2 rounded-2xl font-black text-sm">
                    <i data-feather="clock" class="w-4 h-4"></i>
                    Waiting for Shipment
                </button>
                <p class="text-[10px] text-center text-gray-400 mt-2 italic">
                    You can log receipt once the vendor marks the order as shipped.
                </p>
            @endif
        @endif
    </div>
</div>

{{-- Invoices Card --}}
@if($purchaseOrder->invoices && $purchaseOrder->invoices->count() > 0)
<div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm mt-8">
    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Invoices & Billing</h3>
    <div class="space-y-4">
        @foreach($purchaseOrder->invoices as $invoice)
            <div class="bg-gray-50 dark:bg-gray-900/20 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-wider">{{ $invoice->invoice_number }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase">{{ $invoice->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($invoice->status === 'proforma')
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-[8px] font-black rounded uppercase tracking-widest">Proforma</span>
                    @else
                        <span class="px-2 py-1 bg-gray-200 text-gray-700 text-[8px] font-black rounded uppercase tracking-widest">{{ $invoice->status }}</span>
                    @endif
                </div>
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-sm font-black text-gray-900 dark:text-white">{{ $invoice->formatted_total_amount }}</span>
                    <a href="{{ route('procurement.invoices.download-pdf', $invoice) }}" class="text-[9px] font-black uppercase text-primary-600 tracking-widest hover:underline flex items-center gap-1">
                        <i data-feather="download" class="w-3 h-3"></i> Download
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
