@extends('layouts.app', [
    'title' => 'Received Invoices',
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
               class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all bg-primary-600 text-white shadow-lg shadow-primary-600/20">
                Received
            </a>
            <a href="{{ route('procurement.invoices.index', ['view' => 'vendor']) }}"
               class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all text-gray-400 hover:bg-gray-50">
                Sent
            </a>
        </div>
    </div>

    @if($recentBuyerInvoices->isNotEmpty())
        <div class="mb-10">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-6">Recent Activity</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($recentBuyerInvoices as $invoice)
                    <a href="{{ route('procurement.invoices.show', $invoice) }}" 
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-primary-600/5 transition-all group overflow-hidden">
                        <div class="flex flex-col h-full">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-2 py-1 bg-primary-100 text-primary-700 rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $invoice->invoice_number }}</span>
                                @if($invoice->created_at->gt(now()->subDay()))
                                    <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(37,99,235,0.6)]"></span>
                                @endif
                            </div>
                            <p class="text-[11px] font-black text-gray-900 dark:text-white truncate mb-1 uppercase tracking-tight">{{ $invoice->purchaseOrder->vendorCompany->name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-6">{{ $invoice->created_at->diffForHumans() }}</p>
                            
                            <div class="mt-auto flex items-end justify-between">
                                <div>
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">TOTAL</p>
                                    <p class="text-sm font-black text-gray-900 dark:text-white">{{ $invoice->formatted_total_amount }}</p>
                                </div>
                                <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-primary-600 transition-colors group-hover:bg-primary-600 group-hover:text-white">
                                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div id="buyerContent" class="space-y-12">
        @if($buyerInvoices->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-100 dark:border-gray-600 text-gray-300">
                    <i data-feather="file-text" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-[0.2em]">No received invoices</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">When vendors submit invoices, they will appear here</p>
            </div>
        @else
            @foreach($buyerInvoices as $poId => $invoices)
                @php
                    $firstInvoice = $invoices->first();
                    $po = $firstInvoice->purchaseOrder;
                @endphp
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-6">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">PURCHASE ORDER</span>
                            <span class="px-2 py-0.5 bg-gray-900 text-white rounded-md text-[9px] font-black uppercase tracking-widest">{{ $po->po_number }}</span>
                        </div>
                        <a href="{{ route('procurement.po.show', $po) }}" class="text-[9px] font-black text-primary-600 uppercase tracking-widest hover:underline transition-all">TRACK PO →</a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($invoices as $invoice)
                            <a href="{{ route('procurement.invoices.show', $invoice) }}" 
                                class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-primary-600/5 transition-all group">
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $invoice->invoice_number }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">DUE: {{ $invoice->due_date->format('d M Y') }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 text-[9px] font-black rounded-md uppercase tracking-wider
                                        @if($invoice->status === 'matched' || $invoice->status === 'paid') bg-green-50 text-green-600 border border-green-100
                                        @elseif($invoice->status === 'mismatch') bg-red-50 text-red-600 border border-red-100
                                        @else bg-yellow-50 text-yellow-600 border border-yellow-100 @endif leading-none">
                                        {{ $invoice->status }}
                                    </span>
                                </div>
                                
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">AMOUNT</p>
                                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $invoice->formatted_total_amount }}</p>
                                    </div>
                                    <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-primary-600 transition-colors group-hover:bg-primary-600 group-hover:text-white">
                                        <i data-feather="chevron-right" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
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
