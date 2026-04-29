{{-- Address Blocks & Info --}}
<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
    <div class="p-8 md:p-12">
        <div class="flex flex-col md:flex-row justify-between gap-12 mb-16">
            <div>
                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Vendor</h2>
                <p class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">{{ $purchaseOrder->vendorCompany?->name ?? $purchaseOrder->historical_vendor_name ?? 'N/A' }}</p>
                <div class="text-[11px] font-bold text-gray-500 space-y-1">
                    <p>{{ $purchaseOrder->vendorCompany?->address ?? 'No address provided' }}</p>
                    <p class="text-primary-600">{{ $purchaseOrder->vendorCompany?->email ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                    @if($purchaseOrder->purchase_type)
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Type</p>
                        <p class="text-[11px] font-bold text-gray-900 dark:text-white uppercase">{{ $purchaseOrder->purchase_type }}</p>
                    </div>
                    @endif
                    @if($purchaseOrder->dept)
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Dept</p>
                        <p class="text-[11px] font-bold text-gray-900 dark:text-white uppercase">{{ $purchaseOrder->dept }}</p>
                    </div>
                    @endif
                    @if($purchaseOrder->month)
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Month</p>
                        <p class="text-[11px] font-bold text-gray-900 dark:text-white uppercase">{{ $purchaseOrder->month }}</p>
                    </div>
                    @endif
                    @if($purchaseOrder->currency)
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Currency</p>
                        <p class="text-[11px] font-bold text-gray-900 dark:text-white uppercase">{{ $purchaseOrder->currency }}</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="md:text-right">
                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Ship To</h2>
                <p class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">{{ $purchaseOrder->purchaseRequisition?->company->name ?? $purchaseOrder->buyerCompany?->name ?? 'N/A' }}</p>
                <div class="text-[11px] font-bold text-gray-500 space-y-1 uppercase tracking-tighter">
                    <p>{{ $purchaseOrder->purchaseRequisition?->delivery_point ?? 'Head Office' }}</p>
                    <p>Attn: {{ $purchaseOrder->createdBy->name }}</p>
                    @if($purchaseOrder->approved_by_user_id)
                        <p>Approved: {{ $purchaseOrder->approvedBy?->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Item Table --}}
        <div class="mb-16">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Line Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-50 dark:border-gray-700">
                            <th class="py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Description</th>
                            <th class="py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest px-4">Qty</th>
                            <th class="py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Rate</th>
                            <th class="py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @foreach($purchaseOrder->items as $item)
                            <tr class="group border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                                <td class="py-6">
                                    <div class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                        {{ $item->purchaseRequisitionItem?->catalogueItem?->name ?? $item->item_name ?? 'N/A' }}
                                    </div>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                                        <div class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">
                                            SKU: {{ $item->purchaseRequisitionItem?->catalogueItem?->sku ?? 'N/A' }}
                                        </div>
                                        @if($item->category)
                                        <div class="text-[9px] text-primary-600 font-bold uppercase tracking-widest">
                                            CAT: {{ $item->category }}
                                        </div>
                                        @endif
                                        @if($item->business_category)
                                        <div class="text-[9px] text-indigo-600 font-bold uppercase tracking-widest">
                                            BIZ: {{ $item->business_category }}
                                        </div>
                                        @endif
                                    </div>
                                    @if($item->specifications)
                                    <div class="mt-2 text-[10px] text-gray-500 font-medium italic bg-gray-50 dark:bg-gray-900/30 p-2 rounded-lg border border-gray-100 dark:border-gray-700/50">
                                        {{ $item->specifications }}
                                    </div>
                                    @endif
                                </td>
                                <td class="py-6 text-center">
                                    <div class="font-bold text-gray-700 dark:text-gray-300">{{ $item->quantity_ordered }}</div>
                                    @if($item->unit)
                                    <div class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-1">{{ $item->unit }}</div>
                                    @endif
                                </td>
                                <td class="py-6 text-right">
                                    <div class="font-bold text-gray-400">{{ $item->formatted_unit_price }}</div>
                                    @if($item->tax_amount > 0)
                                    <div class="text-[9px] text-red-400 font-bold mt-1">+TAX: Rp {{ number_format($item->tax_amount, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="py-6 text-right">
                                    <div class="font-black text-gray-900 dark:text-white tabular-nums">{{ $item->formatted_subtotal }}</div>
                                    @if($item->total_inc_tax > 0)
                                    <div class="text-[9px] text-gray-400 font-bold mt-1">INC TAX</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Final Calculations --}}
        <div class="flex justify-end pt-8 border-t border-gray-100 dark:border-gray-700">
            <div class="w-full md:w-80 space-y-6">
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Grand Total</span>
                    <span class="text-2xl font-black text-primary-600 tabular-nums">
                        {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Negotiated Terms --}}
        @if($purchaseOrder->offer)
        <div class="mt-8 mb-8 p-6 bg-gray-50 dark:bg-gray-900/20 rounded-2xl border border-gray-100 dark:border-gray-700">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Negotiated Terms</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Payment Scheme</p>
                    <p class="text-[11px] font-bold text-gray-900 dark:text-white uppercase">{{ $purchaseOrder->offer->payment_scheme ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Promised Delivery</p>
                    <p class="text-[11px] font-bold text-gray-900 dark:text-white uppercase">{{ $purchaseOrder->offer->delivery_time ?? 'N/A' }} Days</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Warranty</p>
                    <p class="text-[11px] font-bold text-gray-900 dark:text-white uppercase">{{ $purchaseOrder->offer->warranty ?? 'N/A' }} Months</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Footer Notes --}}
        <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-700 grid md:grid-cols-2 gap-8">
            <div>
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Terms & Conditions</h4>
                <p class="text-sm text-gray-500 italic leading-relaxed">
                    This purchase order is subject to the standard terms and conditions. 
                    Please ensure all deliveries reference PO# {{ $purchaseOrder->po_number }}.
                </p>
            </div>
            <div class="md:text-right flex flex-col items-end justify-end">
                <div class="w-32 h-16 bg-gray-50 dark:bg-gray-900/30 rounded-xl mb-2 flex items-center justify-center border border-dashed border-gray-200 dark:border-gray-600">
                    <span class="text-[10px] text-gray-400 uppercase font-black uppercase">Authorized</span>
                </div>
                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $purchaseOrder->purchaseRequisition?->company->name ?? $purchaseOrder->buyerCompany?->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
