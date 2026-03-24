@extends('layouts.app', [
    'title' => 'Purchase Order: ' . $purchaseOrder->po_number,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => route('procurement.po.index')],
        ['name' => $purchaseOrder->po_number, 'url' => null],
    ]
])

@section('content')
    <div class="max-w-5xl mx-auto space-y-8">
        {{-- Top Navigation & Global Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('procurement.po.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition">
                    <i data-feather="arrow-left" class="w-5 h-5 text-gray-500"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $purchaseOrder->po_number }}</h1>
                    <p class="text-sm text-gray-500 font-medium tracking-wide uppercase">Purchase Order</p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('procurement.po.print', $purchaseOrder) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-bold rounded-xl hover:shadow-md transition">
                    <i data-feather="printer" class="w-4 h-4 text-primary-500"></i>
                    Print PDF
                </a>

                @if($isBuyer && in_array($purchaseOrder->status, ['issued', 'confirmed']) && $purchaseOrder->escrow_status === 'pending')
                    <button onclick="document.getElementById('escrowPayModal').classList.remove('hidden')" 
                            class="px-6 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition text-sm font-bold shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                        <i data-feather="shield" class="w-4 h-4"></i>
                        Bayar Escrow
                    </button>
                @endif

                @if($isBuyer && $purchaseOrder->status === 'full_delivery' && $purchaseOrder->escrow_status === 'paid')
                    <form action="{{ route('procurement.po.escrow-release', $purchaseOrder) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition text-sm font-bold shadow-lg shadow-green-500/20 flex items-center gap-2">
                            <i data-feather="unlock" class="w-4 h-4"></i>
                            Release Escrow
                        </button>
                    </form>
                @endif

                @if($isVendor && $purchaseOrder->status === 'pending_vendor_acceptance')
                    <form action="{{ route('procurement.po.vendor-reject', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Reject this PO?')">
                        @csrf
                        <button type="submit" class="px-5 py-2 bg-red-50 text-red-700 border border-red-100 rounded-xl hover:bg-red-100 transition text-sm font-bold">
                            Reject
                        </button>
                    </form>
                    <form action="{{ route('procurement.po.vendor-accept', $purchaseOrder) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition text-sm font-bold shadow-lg shadow-primary-500/20">
                            Accept & Confirm
                        </button>
                    </form>
                @endif

                @if($isVendor && $purchaseOrder->status === 'issued')
                    <a href="{{ route('procurement.do.create', $purchaseOrder) }}" 
                       class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition text-sm font-bold shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                        <i data-feather="truck" class="w-4 h-4"></i>
                        Arrange Delivery
                    </a>
                @endif
            </div>
        </div>

        {{-- Visual Stepper --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
            @php
                $status = $purchaseOrder->status;
                $totalOrdered = $purchaseOrder->items->sum('quantity_ordered');
                $totalReceived = $purchaseOrder->items->sum('quantity_received');
                $escrow = $purchaseOrder->escrow_status;
                
                $steps = [
                    ['label' => 'Order Sent', 'status' => 'completed'],
                    ['label' => 'Accepted', 'status' => in_array($status, ['issued', 'partial_delivery', 'full_delivery', 'completed']) ? 'completed' : 'pending'],
                    ['label' => 'Escrow Paid', 'status' => in_array($escrow, ['paid', 'released']) ? 'completed' : ($escrow === 'disputed' ? 'error' : 'pending')],
                    ['label' => 'Shipping', 'status' => $purchaseOrder->deliveryOrders->where('status', 'shipped')->count() > 0 ? 'completed' : 'pending'],
                    ['label' => 'Received', 'status' => in_array($status, ['partial_delivery', 'full_delivery', 'completed']) ? 'completed' : 'pending'],
                    ['label' => 'Released', 'status' => $escrow === 'released' ? 'completed' : ($escrow === 'refunded' ? 'error' : 'pending')],
                ];

                if ($status === 'cancelled' || $status === 'rejected_by_vendor') {
                    $steps[1] = ['label' => 'Rejected', 'status' => 'error'];
                }

                // Next Action
                $nextAction = '';
                $nextRole = '';
                
                if ($status === 'pending_vendor_acceptance') {
                    $nextAction = 'Waiting for Vendor to review and accept this order.';
                    $nextRole = $isVendor ? 'PLEASE ACCEPT' : 'WAITING FOR VENDOR';
                } elseif (in_array($status, ['issued', 'confirmed']) && $escrow === 'pending') {
                    $nextAction = 'Order accepted by vendor. Buyer perlu melakukan pembayaran ke rekening Escrow.';
                    $nextRole = $isBuyer ? 'BAYAR ESCROW' : 'WAITING FOR ESCROW';
                } elseif (in_array($status, ['issued', 'confirmed']) && $escrow === 'paid') {
                    $hasShipped = $purchaseOrder->deliveryOrders->where('status', 'shipped')->count() > 0;
                    if ($hasShipped) {
                        $nextAction = 'Barang telah dikirim. Buyer bisa melakukan penerimaan barang.';
                        $nextRole = $isBuyer ? 'LOG RECEIPT' : 'SHIPPED';
                    } else {
                        $nextAction = 'Dana escrow sudah aman. Vendor dapat mengirimkan barang.';
                        $nextRole = $isVendor ? 'PLEASE SHIP' : 'WAITING FOR SHIPMENT';
                    }
                } elseif ($status === 'partial_delivery') {
                    $nextAction = 'Sebagian barang sudah diterima. Menunggu sisa item atau penerimaan berikutnya.';
                    $nextRole = $isVendor ? 'SHIP REMAINING' : 'LOG NEXT RECEIPT';
                } elseif ($status === 'full_delivery' && $escrow === 'paid') {
                    $nextAction = 'Semua barang diterima. 3-Way Matching berjalan — dana escrow siap dicairkan.';
                    $nextRole = $isBuyer ? 'RELEASE ESCROW' : 'WAITING RELEASE';
                } elseif ($status === 'full_delivery' && $escrow === 'released') {
                    $nextAction = 'Semua barang diterima dan dana telah dicairkan ke vendor. Transaksi selesai.';
                    $nextRole = 'COMPLETED';
                } elseif ($status === 'completed') {
                    $nextAction = 'Order selesai — barang diterima dan dana escrow telah dicairkan.';
                    $nextRole = 'COMPLETED';
                }
            @endphp
            <div class="flex items-center justify-between max-w-3xl mx-auto relative px-4">
                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center relative z-10">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full border-2 
                            @if($step['status'] === 'completed') bg-primary-600 border-primary-600 @elseif($step['status'] === 'error') bg-red-500 border-red-500 @else bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 @endif
                            transition-all duration-300">
                            @if($step['status'] === 'completed')
                                <i data-feather="check" class="w-5 h-5 text-white"></i>
                            @elseif($step['status'] === 'error')
                                <i data-feather="x" class="w-5 h-5 text-white"></i>
                            @else
                                <span class="text-gray-400 font-bold text-sm">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <span class="mt-2 text-xs font-bold uppercase tracking-widest mt-3
                            @if($step['status'] === 'completed') text-primary-600 @elseif($step['status'] === 'error') text-red-500 @else text-gray-400 @endif">
                            {{ $step['label'] }}
                        </span>
                    </div>
                    @if(!$loop->last)
                        <div class="absolute h-0.5 bg-gray-100 dark:bg-gray-700 top-[20px]" 
                             style="left: {{ ($index * 25) + 5 }}%; right: {{ 100 - (($index + 1) * 25) + 5 }}%;">
                             <div class="h-full @if($steps[$index+1]['status'] === 'completed') bg-primary-600 @else bg-gray-100 dark:bg-gray-700 @endif transition-all duration-300"></div>
                        </div>
                    @endif
                @endforeach
            </div>

            @if($nextAction)
                <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center gap-6">
                    <div class="px-4 py-1.5 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400 rounded-full text-[10px] font-black tracking-widest uppercase shrink-0">
                        {{ $nextRole }}
                    </div>
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300">
                        {{ $nextAction }}
                    </p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main PO Document Area --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Address Blocks & Info --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 md:p-12">
                        {{-- Document Header --}}
                        <div class="flex flex-col md:flex-row justify-between gap-8 mb-12">
                            <div>
                                <h2 class="text-sm font-black text-primary-600 uppercase tracking-widest mb-4">Vendor Information</h2>
                                <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $purchaseOrder->vendorCompany?->name ?? $purchaseOrder->historical_vendor_name ?? 'N/A' }}</p>
                                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <p class="flex items-center gap-2">
                                        <i data-feather="map-pin" class="w-3 h-3"></i>
                                        {{ $purchaseOrder->vendorCompany?->address ?? 'No address provided' }}
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <i data-feather="mail" class="w-3 h-3"></i>
                                        {{ $purchaseOrder->vendorCompany?->email ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="md:text-right">
                                <h2 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Ship To</h2>
                                <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $purchaseOrder->purchaseRequisition?->company->name ?? $purchaseOrder->buyerCompany?->name ?? 'N/A' }}</p>
                                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <p class="flex items-center gap-2 md:justify-end">
                                        {{ $purchaseOrder->purchaseRequisition?->delivery_point ?? 'Head Office' }}
                                        <i data-feather="map-pin" class="w-3 h-3"></i>
                                    </p>
                                    <p class="flex items-center gap-2 md:justify-end">
                                        Attn: {{ $purchaseOrder->createdBy->name }}
                                        <i data-feather="user" class="w-3 h-3"></i>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Item Table --}}
                        <div class="mb-12">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Line Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <th class="py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Description</th>
                                            <th class="py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest px-4">Qty</th>
                                            <th class="py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest px-4">Accepted</th>
                                            <th class="py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Rate</th>
                                            <th class="py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                        @foreach($purchaseOrder->items as $item)
                                            <tr>
                                                <td class="py-6">
                                                    <div class="font-bold text-gray-900 dark:text-white">
                                                        {{ $item->purchaseRequisitionItem?->catalogueItem?->name ?? $item->item_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1 uppercase tracking-tighter">
                                                        SKU: {{ $item->purchaseRequisitionItem?->catalogueItem?->sku ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td class="py-6 text-center font-bold text-gray-700 dark:text-gray-300 tabular-nums">{{ $item->quantity_ordered }}</td>
                                                <td class="py-6 text-center tabular-nums">
                                                    <span class="px-2 py-1 rounded-lg text-xs font-black
                                                        @if($item->quantity_received >= $item->quantity_ordered) bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 @elseif($item->quantity_received > 0) bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 @else text-gray-400 @endif">
                                                        {{ $item->quantity_received }}
                                                    </span>
                                                </td>
                                                <td class="py-6 text-right font-medium text-gray-600 dark:text-gray-400 tabular-nums">{{ $item->formatted_unit_price }}</td>
                                                <td class="py-6 text-right font-black text-gray-900 dark:text-white tabular-nums">{{ $item->formatted_subtotal }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Final Calculations --}}
                        <div class="flex justify-end pt-8 border-t-2 border-dashed border-gray-100 dark:border-gray-700">
                            <div class="w-full md:w-64 space-y-4">
                                <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                    <span class="text-sm font-bold uppercase tracking-widest">Subtotal</span>
                                    <span class="font-bold tabular-nums">{{ $purchaseOrder->formatted_total_amount }}</span>
                                </div>
                                @if($purchaseOrder->has_deductions)
                                    <div class="flex justify-between items-center text-red-600 font-bold">
                                        <span class="text-sm uppercase tracking-widest">Deductions</span>
                                        <span class="tabular-nums">- {{ $purchaseOrder->formatted_total_deduction }}</span>
                                    </div>
                                @endif
                                <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                    <span class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tighter">Grand Total</span>
                                    <span class="text-2xl font-black text-primary-600 tabular-nums">
                                        {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                                    </span>
                                </div>
                            </div>
                        </div>

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
                                <div class="w-32 h-16 bg-gray-50 dark:bg-gray-700/30 rounded-xl mb-2 flex items-center justify-center border border-dashed border-gray-200 dark:border-gray-600">
                                    <span class="text-[10px] text-gray-400 uppercase font-black uppercase">Authorized Signature</span>
                                </div>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $purchaseOrder->purchaseRequisition?->company->name ?? $purchaseOrder->buyerCompany?->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivery Logs --}}
                @if($purchaseOrder->deliveryOrders->isNotEmpty())
                    <div class="space-y-4">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest px-4">Shipment History</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($purchaseOrder->deliveryOrders as $do)
                                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-center gap-4 group hover:shadow-lg hover:shadow-indigo-500/5 transition duration-300">
                                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center text-indigo-600">
                                        <i data-feather="package" class="w-6 h-6"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $do->do_number }}</p>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest
                                                @if($do->status === 'shipped') bg-blue-50 text-blue-600 @else bg-gray-50 text-gray-500 @endif">
                                                {{ $do->status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            @if($do->status === 'shipped')
                                                Shipped {{ $do->shipped_at->diffForHumans() }}
                                            @else
                                                Created {{ $do->created_at->format('d M') }}
                                            @endif
                                        </p>
                                    </div>
                                    @if($do->status === 'pending' && $isVendor)
                                        <button onclick="shipOrder('{{ $do->id }}')" class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-lg shadow-indigo-500/20">
                                            <i data-feather="send" class="w-4 h-4"></i>
                                        </button>
                                        <form id="ship-form-{{ $do->id }}" action="{{ route('procurement.do.ship', $do) }}" method="POST" class="hidden">
                                            @csrf
                                            <input type="hidden" name="tracking_number" id="tracking-input-{{ $do->id }}">
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar Insights --}}
            <div class="space-y-8">
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

                {{-- Status Feed --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Status History</h3>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-2 h-2 rounded-full bg-primary-600 shadow-[0_0_8px_rgba(37,99,235,0.6)]"></div>
                                <div class="w-0.5 flex-1 bg-gray-100 dark:bg-gray-700 mt-2"></div>
                            </div>
                            <div>
                                <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">Current Stage</p>
                                <p class="text-xs text-gray-500 mt-1 capitalize">{{ str_replace('_', ' ', $purchaseOrder->status) }}</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Issued On</p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ $purchaseOrder->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Escrow Payment Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Escrow Payment</h3>
                    
                    @if($purchaseOrder->escrow_status === 'pending')
                        <div class="text-center py-4">
                            <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-feather="clock" class="w-6 h-6 text-amber-500"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Menunggu Pembayaran</p>
                            <p class="text-xs text-gray-500 mt-1">Buyer perlu membayar ke rekening escrow.</p>
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/20 rounded-xl border border-gray-100 dark:border-gray-700">
                                <p class="text-[10px] font-black text-gray-400 uppercase">Amount</p>
                                <p class="text-lg font-black text-primary-600 tabular-nums mt-1">
                                    {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                                </p>
                            </div>
                        </div>
                    @elseif($purchaseOrder->escrow_status === 'paid')
                        <div class="text-center py-4">
                            <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-feather="shield" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <p class="text-sm font-bold text-blue-700 dark:text-blue-400">Dana di Escrow</p>
                            <p class="text-xs text-gray-500 mt-1">Dana aman. Menunggu barang diterima.</p>
                            <div class="mt-4 space-y-2">
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-800/50">
                                    <p class="text-[10px] font-black text-blue-500 uppercase">Amount Secured</p>
                                    <p class="text-lg font-black text-blue-700 dark:text-blue-400 tabular-nums mt-1">
                                        {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                                    </p>
                                </div>
                                @if($purchaseOrder->escrow_reference)
                                    <div class="text-left p-3 bg-gray-50 dark:bg-gray-900/20 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <p class="text-[10px] font-black text-gray-400 uppercase">Reference</p>
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mt-1">{{ $purchaseOrder->escrow_reference }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $purchaseOrder->escrow_paid_at->format('d M Y, H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($purchaseOrder->escrow_status === 'released')
                        <div class="text-center py-4">
                            <div class="w-14 h-14 bg-green-50 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <p class="text-sm font-bold text-green-700 dark:text-green-400">Dana Dicairkan</p>
                            <p class="text-xs text-gray-500 mt-1">Dana telah berhasil ditransfer ke vendor.</p>
                            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/10 rounded-xl border border-green-100 dark:border-green-800/50">
                                <p class="text-[10px] font-black text-green-500 uppercase">Released Amount</p>
                                <p class="text-lg font-black text-green-700 dark:text-green-400 tabular-nums mt-1">
                                    {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                                </p>
                                @if($purchaseOrder->escrow_released_at)
                                    <p class="text-[10px] text-green-600 mt-1">{{ $purchaseOrder->escrow_released_at->format('d M Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Invoices --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Invoices</h3>
                        @if($isVendor)
                            <a href="{{ route('procurement.invoices.create', $purchaseOrder) }}" class="p-2 bg-primary-50 text-primary-600 rounded-xl hover:bg-primary-600 hover:text-white transition">
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
            </div>
        </div>
    </div>

    {{-- Escrow Payment Modal --}}
    <div id="escrowPayModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md relative overflow-hidden transform transition-all">
            {{-- Header Decor --}}
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-primary-400 to-primary-600"></div>
            
            <button onclick="document.getElementById('escrowPayModal').classList.add('hidden')" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>

            <div class="p-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary-50 dark:bg-primary-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-primary-100 dark:border-primary-800/50">
                        <i data-feather="shield" class="w-8 h-8 text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Pembayaran Escrow</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Amankan transaksi Anda dengan deposit dana ke sistem escrow kami.</p>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
                    <div class="flex justify-between items-center mb-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Tagihan</p>
                        <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-[10px] font-bold rounded-full">MUST PAY</span>
                    </div>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums">
                        {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                    </p>
                </div>

                <form action="{{ route('procurement.po.escrow-pay', $purchaseOrder) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No. Referensi / Bukti Transfer</label>
                            <div class="relative">
                                <i data-feather="hash" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="escrow_reference" required placeholder="Contoh: TRX-2026-XXXX" 
                                       class="w-full pl-11 pr-4 py-3.5 bg-white dark:bg-gray-900 rounded-xl border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 transition-all font-bold tabular-nums">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 italic">Pastikan nomor referensi sesuai dengan bukti transfer Anda.</p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" onclick="document.getElementById('escrowPayModal').classList.add('hidden')" 
                                    class="flex-1 py-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 font-bold transition-all">
                                Batal
                            </button>
                            <button type="submit" class="flex-2 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0">
                                <i data-feather="check-circle" class="w-5 h-5"></i>
                                Konfirmasi Bayar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- SweetAlert2 loaded via Vite --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        function handlePrFormSubmit(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.innerHTML = '<div class="flex items-center gap-2"><div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>Processing...</div>';
            }
            return true;
        }

        function shipOrder(doId) {
            Swal.fire({
                title: 'Mark as Shipped',
                text: 'Please enter the shipping tracking number (Resi):',
                input: 'text',
                inputPlaceholder: 'e.g. JB0018829922',
                showCancelButton: true,
                confirmButtonText: 'Ship Order',
                confirmButtonColor: '#4F46E5', // Indigo-600
                showLoaderOnConfirm: true,
                preConfirm: (trackingNumber) => {
                    if (!trackingNumber) {
                        Swal.showValidationMessage('Tracking number is required');
                    }
                    return trackingNumber;
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('tracking-input-' + doId).value = result.value;
                    document.getElementById('ship-form-' + doId).submit();
                }
            });
        }
    </script>
@endpush
