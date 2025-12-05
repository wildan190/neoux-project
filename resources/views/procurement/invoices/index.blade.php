@extends('layouts.app', [
    'title' => 'Invoices',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Invoices', 'url' => null],
    ]
])

@section('content')
    {{-- Tabs Navigation --}}
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <button onclick="switchTab('buyer')" id="buyerTab"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                           border-primary-500 text-primary-600 dark:text-primary-400">
                    <i data-feather="file-text" class="w-4 h-4 inline mr-2"></i>
                    Received Invoices
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                        {{ $buyerInvoices->flatten()->count() }}
                    </span>
                </button>
                <button onclick="switchTab('vendor')" id="vendorTab"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                           border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                    Submitted Invoices
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                        {{ $vendorInvoices->flatten()->count() }}
                    </span>
                </button>
            </nav>
        </div>
    </div>

    {{-- Buyer Invoices Tab --}}
    <div id="buyerContent" class="tab-content space-y-6">
        @if($buyerInvoices->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-700">
                <i data-feather="file-text" class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"></i>
                <p class="text-lg font-bold text-gray-900 dark:text-white">No Invoices Received</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Invoices from vendors will appear here</p>
            </div>
        @else
            @foreach($buyerInvoices as $poId => $invoices)
                @php
                    $firstInvoice = $invoices->first();
                    $po = $firstInvoice->purchaseOrder;
                @endphp
                
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    {{-- PO Header --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10 border-b border-blue-200 dark:border-blue-800">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    <i data-feather="shopping-cart" class="w-5 h-5 inline mr-2"></i>
                                    PO: {{ $po->po_number }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Vendor: <strong>{{ $po->vendorCompany->name }}</strong> • 
                                    Total: <strong>{{ $po->formatted_total_amount }}</strong>
                                </p>
                            </div>
                            <a href="{{ route('procurement.po.show', $po) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                View PO →
                            </a>
                        </div>
                    </div>

                    {{-- Invoices for this PO --}}
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($invoices as $invoice)
                                <a href="{{ route('procurement.invoices.show', $invoice) }}" 
                                   class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-600">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</h4>
                                            <span class="px-2 py-1 text-xs font-bold rounded 
                                                @if($invoice->status === 'matched') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($invoice->status === 'mismatch') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $invoice->invoice_date->format('d M Y') }} • Due: {{ $invoice->due_date->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->formatted_total_amount }}</p>
                                        <i data-feather="chevron-right" class="w-4 h-4 text-gray-400 inline"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Vendor Invoices Tab --}}
    <div id="vendorContent" class="tab-content hidden space-y-6">
        @if($vendorInvoices->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-700">
                <i data-feather="send" class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"></i>
                <p class="text-lg font-bold text-gray-900 dark:text-white">No Invoices Submitted</p>
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
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/10 border-b border-green-200 dark:border-green-800">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    <i data-feather="shopping-cart" class="w-5 h-5 inline mr-2"></i>
                                    PO: {{ $po->po_number }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Buyer: <strong>{{ $po->purchaseRequisition->company->name }}</strong> • 
                                    Total: <strong>{{ $po->formatted_total_amount }}</strong>
                                </p>
                            </div>
                            <a href="{{ route('procurement.po.show', $po) }}" class="text-sm font-semibold text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                                View PO →
                            </a>
                        </div>
                    </div>

                    {{-- Invoices for this PO --}}
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($invoices as $invoice)
                                <a href="{{ route('procurement.invoices.show', $invoice) }}" 
                                   class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-600">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</h4>
                                            <span class="px-2 py-1 text-xs font-bold rounded 
                                                @if($invoice->status === 'matched') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($invoice->status === 'mismatch') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $invoice->invoice_date->format('d M Y') }} • Due: {{ $invoice->due_date->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->formatted_total_amount }}</p>
                                        <i data-feather="chevron-right" class="w-4 h-4 text-gray-400 inline"></i>
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
        function switchTab(tab) {
            const buyerTab = document.getElementById('buyerTab');
            const vendorTab = document.getElementById('vendorTab');
            const buyerContent = document.getElementById('buyerContent');
            const vendorContent = document.getElementById('vendorContent');
            
            if (tab === 'buyer') {
                buyerTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                buyerTab.classList.add('border-primary-500', 'text-primary-600');
                vendorTab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                vendorTab.classList.remove('border-primary-500', 'text-primary-600');
                
                buyerContent.classList.remove('hidden');
                vendorContent.classList.add('hidden');
            } else {
                vendorTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                vendorTab.classList.add('border-primary-500', 'text-primary-600');
                buyerTab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                buyerTab.classList.remove('border-primary-500', 'text-primary-600');
                
                vendorContent.classList.remove('hidden');
                buyerContent.classList.add('hidden');
            }
            
            feather.replace();
        }
        
        feather.replace();
    </script>
@endpush
