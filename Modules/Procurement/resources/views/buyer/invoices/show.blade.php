@extends('layouts.app', [
    'title' => 'Invoice Detail',
    'breadcrumbs' => [
        ['name' => 'Maps', 'url' => '#'],
        ['name' => 'Finance', 'url' => '#'],
        ['name' => 'Invoices', 'url' => route('procurement.invoices.index')],
        ['name' => $invoice->invoice_number, 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $invoice->invoice_number }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $invoice->created_at->format('M d, Y') }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Invoice Detail</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('procurement.invoices.print', $invoice) }}" target="_blank"
                class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition-all">
                Print
            </a>
            <a href="{{ route('procurement.invoices.download-pdf', $invoice) }}"
                class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all">
                Download PDF
            </a>
        </div>
    </div>

    {{-- The Map: Invoice Lifecycle --}}
    @include('procurement::partials.invoice_lifecycle_stepper')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Area --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Invoice Items --}}
            @include('procurement::partials.invoice_items_card')

            {{-- Matching Results --}}
            @if($invoice->match_status)
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="p-8">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Three-Way Matching Report</h3>
                            @if($invoice->status === 'matched')
                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-green-100">MATCHED</span>
                            @elseif($invoice->status === 'mismatch')
                                <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-red-100">MISMATCH</span>
                            @endif
                        </div>

                        @if(isset($invoice->match_status['variances']) && count($invoice->match_status['variances']) > 0)
                            <div class="mb-8 p-6 bg-red-50 dark:bg-red-900/10 rounded-2xl border border-red-100 dark:border-red-800">
                                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-3">Variances Detected</p>
                                <ul class="space-y-2">
                                    @foreach($invoice->match_status['variances'] as $variance)
                                        <li class="flex items-center gap-2 text-[11px] font-bold text-red-700 dark:text-red-400 uppercase tracking-tight">
                                            <i data-feather="alert-circle" class="w-3 h-3"></i>
                                            {{ $variance }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-50 dark:border-gray-700">
                                        <th class="py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Item Description</th>
                                        <th class="py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">Pricing</th>
                                        <th class="py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">Quantities</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    @foreach($invoice->match_status['details'] as $detail)
                                        @php
                                            $item = $invoice->items->first(fn($i) => $i->purchase_order_item_id == $detail['item_id']);
                                            $itemName = $item ? ($item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? $item->purchaseOrderItem->item_name) : 'Unknown';
                                        @endphp
                                        <tr>
                                            <td class="py-6">
                                                <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $itemName }}</p>
                                            </td>
                                            <td class="py-6">
                                                <div class="flex items-center justify-center gap-4">
                                                    <div class="text-right">
                                                        <p class="text-[8px] font-black text-gray-300 uppercase">PO RATE</p>
                                                        <p class="text-[10px] font-bold text-gray-500">{{ number_format($detail['po_price'], 2) }}</p>
                                                    </div>
                                                    <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $detail['price_match'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                        <i data-feather="{{ $detail['price_match'] ? 'check' : 'x' }}" class="w-3 h-3"></i>
                                                    </div>
                                                    <div class="text-left">
                                                        <p class="text-[8px] font-black text-gray-300 uppercase">INV RATE</p>
                                                        <p class="text-[10px] font-bold text-gray-500">{{ number_format($detail['invoice_price'], 2) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-6">
                                                <div class="flex items-center justify-center gap-4">
                                                    <div class="text-right">
                                                        <p class="text-[8px] font-black text-gray-300 uppercase">RECEIVED</p>
                                                        <p class="text-[10px] font-bold text-gray-500">{{ $detail['total_received'] }}</p>
                                                    </div>
                                                    <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $detail['qty_match'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                        <i data-feather="{{ $detail['qty_match'] ? 'check' : 'x' }}" class="w-3 h-3"></i>
                                                    </div>
                                                    <div class="text-left">
                                                        <p class="text-[8px] font-black text-gray-300 uppercase">INVOICED</p>
                                                        <p class="text-[10px] font-bold text-gray-500">{{ $detail['invoice_qty'] }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Related Context</h3>
                <div class="space-y-4">
                    <a href="{{ route('procurement.po.show', $invoice->purchaseOrder) }}" class="block p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-primary-300 transition-all group">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Purchase Order</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase group-hover:text-primary-600 transition-colors">{{ $invoice->purchaseOrder->po_number }}</p>
                    </a>
                    
                    @if($invoice->purchaseOrder->offer)
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Negotiated Terms</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Payment Scheme</p>
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase">{{ $invoice->purchaseOrder->offer->payment_scheme ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Promised Delivery</p>
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase">{{ $invoice->purchaseOrder->offer->delivery_time ?? 'N/A' }} Days</p>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Warranty</p>
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase">{{ $invoice->purchaseOrder->offer->warranty ?? 'N/A' }} Months</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Workflow Status</h3>
                
                <div class="space-y-4">
                    {{-- Manual approval buttons removed as escrow handles payment lifecycle --}}

                    {{-- Manual rejection removed to shorten the flow --}}

                    @if($invoice->status === 'paid')
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 text-center">
                            <i data-feather="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400 mx-auto mb-2"></i>
                            <p class="text-sm font-bold text-green-900 dark:text-green-100">Transaction Completed</p>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-1">Invoice is fully approved and paid.</p>
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
