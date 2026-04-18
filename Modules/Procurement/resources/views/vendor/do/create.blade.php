@extends('layouts.app', [
    'title' => 'Create Delivery Order: ' . $purchaseOrder->po_number,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'My Sales', 'url' => route('procurement.po.index')],
        ['name' => 'Create DO', 'url' => null],
    ]
])

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-1">
            <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">OUTBOUND LOGISTICS</span>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">ORDER #{{ $purchaseOrder->po_number }}</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-3">
            Initiate <span class="text-primary-600">Shipment</span>
        </h1>
        <p class="text-gray-500 font-medium lowercase">Create a formal delivery order to track items leaving your warehouse.</p>
    </div>

    <form action="{{ route('procurement.do.store', $purchaseOrder) }}" method="POST">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-8">
            <div class="px-10 py-8 border-b border-gray-50 dark:border-gray-800/50 flex justify-between items-center">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Shipping manifest</h3>
                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-900 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-widest">
                    {{ $purchaseOrder->items->count() }} line items
                </span>
            </div>
            
            <div class="p-4 md:p-10 space-y-12">
                @foreach($purchaseOrder->items as $index => $item)
                    @php
                        $remaining = $item->quantity_ordered - $item->quantity_shipped;
                    @endphp
                    
                    @if($remaining > 0)
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
                                        <span>Arranged: {{ $item->quantity_shipped }}</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-indigo-600">Pending: {{ $remaining }}</span>
                                    </div>
                                    <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                </div>

                                <div class="w-full md:w-64 bg-gray-50 dark:bg-gray-900 p-4 rounded-2xl border border-transparent focus-within:border-primary-300 transition-all">
                                    <label for="qty_{{ $index }}" class="block text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Quantity to Ship</label>
                                    <input type="number" name="items[{{ $index }}][quantity_shipped]" 
                                           id="qty_{{ $index }}"
                                           value="{{ $remaining }}"
                                           class="w-full bg-transparent border-0 p-0 text-lg font-black text-gray-900 dark:text-white focus:ring-0"
                                           min="0" max="{{ $remaining }}">
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
                <label for="notes" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Internal shipping Notes</label>
                <textarea name="notes" id="notes" rows="2" 
                          class="w-full bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-800 rounded-2xl text-[11px] font-bold uppercase tracking-tight focus:ring-primary-500 transition-all"
                          placeholder="e.g. Fragile items, special handling instructions..."></textarea>
            </div>
        </div>

        <div class="flex items-center justify-between gap-6">
            <a href="{{ route('procurement.po.show', $purchaseOrder) }}" class="h-16 px-10 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[11px] font-black text-gray-400 uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                Cancel
            </a>
            <button type="submit" class="h-16 flex-1 bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
                Generate Delivery Order
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
</script>
@endpush
