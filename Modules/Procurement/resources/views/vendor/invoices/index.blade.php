@extends('layouts.app', [
    'title' => 'Sent Invoices',
    'breadcrumbs' => [
        ['name' => 'Maps', 'url' => '#'],
        ['name' => 'Finance', 'url' => '#'],
        ['name' => 'Invoices', 'url' => null],
    ]
])

@section('content')
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Invoices</h1>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-1">Manage billing and matching history</p>
        </div>
        <div class="inline-flex p-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl">
            <a href="{{ route('procurement.invoices.index', ['view' => 'buyer']) }}"
               class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all text-gray-400 hover:bg-gray-50">
                Received
            </a>
            <a href="{{ route('procurement.invoices.index', ['view' => 'vendor']) }}"
               class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all bg-primary-600 text-white shadow-lg shadow-primary-600/20">
                Sent
            </a>
        </div>
    </div>

    @if($recentVendorInvoices->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recently Submitted</h3>
                <span class="text-[10px] text-gray-400 uppercase font-medium">Pending Payment</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($recentVendorInvoices as $invoice)
                    <a href="{{ route('procurement.invoices.show', $invoice) }}" 
                        class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/30 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                        @if($invoice->created_at->gt(now()->subDay()))
                            <div class="absolute top-0 right-0">
                                <div class="bg-emerald-500 text-white text-[8px] font-black px-3 py-1 rounded-bl-xl uppercase tracking-tighter">NEW</div>
                            </div>
                        @endif
                        <div class="flex flex-col h-full">
                            <div class="mb-2">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $invoice->invoice_number }}</span>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $invoice->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate mb-1">PO: {{ $invoice->purchaseOrder->po_number }}</p>
                            <p class="text-[10px] text-gray-500 truncate mb-1">To: {{ $invoice->purchaseOrder->purchaseRequisition->company->name ?? 'N/A' }}</p>
                            <p class="text-xs font-black text-gray-700 dark:text-gray-300">{{ $invoice->formatted_total_amount }}</p>
                            <div class="mt-4 pt-3 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-700">{{ $invoice->status }}</span>
                                <i data-feather="arrow-right" class="w-4 h-4 text-emerald-500 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Vendor Invoices Tab --}}
    <div id="vendorContent" class="space-y-6">
        @if($vendorInvoices->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-700">
                <i data-feather="send" class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"></i>
                <p class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">No Invoices Submitted</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Invoices you submit will appear here</p>
            </div>
        @else
            @foreach($vendorInvoices as $poId => $invoices)
                @php
                    $firstInvoice = $invoices->first();
                    $po = $firstInvoice->purchaseOrder;
                @endphp
                
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    {{-- PO Header --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/10 border-b border-emerald-200 dark:border-emerald-800">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">
                                    <i data-feather="shopping-cart" class="w-5 h-5 inline mr-2"></i>
                                    PO: {{ $po->po_number }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Buyer: <strong>{{ $po->purchaseRequisition->company->name ?? 'N/A' }}</strong> • 
                                    Total: <strong>{{ $po->formatted_total_amount }}</strong>
                                </p>
                            </div>
                            <a href="{{ route('procurement.po.show', $po) }}" class="text-sm font-black text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 uppercase tracking-wider">
                                View PO →
                            </a>
                        </div>
                    </div>

                    {{-- Invoices for this PO --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($invoices as $invoice)
                                <a href="{{ route('procurement.invoices.show', $invoice) }}" 
                                    class="group flex flex-col p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-white hover:shadow-xl hover:shadow-emerald-500/10 transition-all border border-gray-200 dark:border-gray-600 hover:border-emerald-300">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $invoice->invoice_number }}</h4>
                                        <span class="px-2 py-0.5 text-[10px] font-black rounded uppercase tracking-wider
                                            @if($invoice->status === 'matched' || $invoice->status === 'paid') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($invoice->status === 'mismatch') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                            @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif leading-none">
                                            {{ $invoice->status }}
                                        </span>
                                    </div>
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Date: {{ $invoice->invoice_date->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Due: {{ $invoice->due_date->format('d M Y') }}</p>
                                    </div>
                                    <div class="mt-auto pt-3 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
                                        <span class="text-sm font-black text-gray-900 dark:text-white">{{ $invoice->formatted_total_amount }}</span>
                                        <i data-feather="chevron-right" class="w-4 h-4 text-gray-400 group-hover:text-emerald-500 transition-colors"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endpush
