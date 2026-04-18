{{-- Invoices Card --}}
<div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Invoices</h3>
        @if($isVendor)
            <a href="{{ route('procurement.invoices.create', $purchaseOrder) }}" class="p-2 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-xl hover:bg-primary-600 hover:text-white transition">
                <i data-feather="plus" class="w-4 h-4"></i>
            </a>
        @endif
    </div>
    
    @if($purchaseOrder->invoices->isEmpty())
        <div class="text-center py-8">
            <div class="w-12 h-12 bg-gray-50 dark:bg-gray-900/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                <i data-feather="file-text" class="w-5 h-5 text-gray-300"></i>
            </div>
            <p class="text-xs text-gray-400 italic font-medium">No invoices yet.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($purchaseOrder->invoices as $invoice)
                <a href="{{ route('procurement.invoices.show', $invoice) }}" 
                   class="block p-4 bg-gray-50 dark:bg-gray-900/20 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-primary-300 transition duration-300">
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-sm font-black text-gray-900 dark:text-white tabular-nums">{{ $invoice->invoice_number }}</p>
                        <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded
                            @if($invoice->status === 'matched') bg-green-50 text-green-600 @else bg-gray-50 text-gray-500 @endif">
                            {{ $invoice->status }}
                        </span>
                    </div>
                    <p class="text-xs font-bold text-gray-500">{{ $invoice->formatted_total_amount }}</p>
                </a>
            @endforeach
        </div>
    @endif
</div>
