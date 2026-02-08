@extends('layouts.app', [
    'title' => $currentView === 'vendor' ? 'Customer Orders (Sales)' : 'My Purchase Orders (Procurement)',
    'breadcrumbs' => [
        ['name' => $currentView === 'vendor' ? 'Sales (Vendor)' : 'Procurement (Buyer)', 'url' => $currentView === 'vendor' ? route('procurement.pr.public-feed') : route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => null],
    ]
])

@section('content')
    {{-- Role Indicator Banner --}}
    <div class="mb-6 rounded-2xl p-4 flex items-center justify-between border {{ $currentView === 'vendor' ? 'bg-emerald-50 border-emerald-100 dark:bg-emerald-900/10 dark:border-emerald-800' : 'bg-primary-50 border-primary-100 dark:bg-primary-900/10 dark:border-primary-800' }}">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $currentView === 'vendor' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' }}">
                <i data-feather="{{ $currentView === 'vendor' ? 'truck' : 'shopping-bag' }}" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Viewing as</h2>
                <p class="text-xl font-black {{ $currentView === 'vendor' ? 'text-emerald-600 dark:text-emerald-400' : 'text-primary-600 dark:text-primary-400' }}">
                    {{ $currentView === 'vendor' ? 'VENDOR (Selling)' : 'BUYER (Buying)' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($currentView === 'buyer')
                <a href="{{ route('procurement.po.export-template') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-bold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <i data-feather="download" class="w-4 h-4 mr-2"></i> Export Template
                </a>
                <button type="button" onclick="openImportModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-primary-500/30">
                    <i data-feather="upload" class="w-4 h-4 mr-2"></i> Import History
                </button>
            @endif
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('procurement.po.index', ['view' => 'buyer']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                           {{ $currentView === 'buyer' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <i data-feather="shopping-bag" class="w-4 h-4 inline mr-2"></i>
                    My Purchases (Buyer)
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs {{ $currentView === 'buyer' ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        {{ $buyerPOs->total() }}
                    </span>
                </a>
                <a href="{{ route('procurement.po.index', ['view' => 'vendor']) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                           {{ $currentView === 'vendor' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <i data-feather="truck" class="w-4 h-4 inline mr-2"></i>
                    Customer Orders (Vendor)
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs {{ $currentView === 'vendor' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        {{ $vendorPOs->total() }}
                    </span>
                </a>
            </nav>
        </div>
    </div>

    @if($currentView === 'buyer')
        {{-- Buyer POs Tab --}}
        <div id="buyerContent">
            <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-primary-50/50 dark:bg-primary-900/10">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Active Purchases</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Orders you have issued to various vendors</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PO Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($buyerPOs as $po)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-primary-600 dark:text-primary-400 leading-none">{{ $po->po_number }}</span>
                                        @if($po->purchaseRequisition)
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">PR: {{ $po->purchaseRequisition->pr_number }}</div>
                                        @else
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 uppercase">Standalone / History</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 leading-none">
                                        {{ $po->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white leading-none">{{ $po->vendorCompany->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white leading-none">
                                        {{ $po->formatted_total_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-[10px] font-black rounded-full uppercase tracking-wider
                                            @if($po->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($po->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                            @elseif($po->status === 'issued') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                            @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                            {{ str_replace('_', ' ', $po->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('procurement.po.show', $po) }}" class="inline-flex items-center text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300 bg-primary-50 dark:bg-primary-900/20 px-3 py-1.5 rounded-lg transition-colors">
                                            View Details <i data-feather="chevron-right" class="w-4 h-4 ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <i data-feather="shopping-bag" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                                        <p class="font-bold">No purchase orders found</p>
                                        <p class="text-xs mt-1">You haven't issued any purchase orders yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($buyerPOs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $buyerPOs->appends(['view' => 'buyer'])->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Vendor POs Tab --}}
        <div id="vendorContent">
            <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-emerald-50/50 dark:bg-emerald-900/10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Orders from Customers</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">New or ongoing orders sent by your clients</p>
                        </div>
                    </div>
                    
                    @if(isset($dashboardFilterLabel))
                        <div class="mt-4 flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center">
                                    <i data-feather="filter" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-amber-800 dark:text-amber-400 uppercase tracking-wider">Active Filter</p>
                                    <p class="text-sm text-amber-700 dark:text-amber-500 font-bold">{{ $dashboardFilterLabel }}</p>
                                </div>
                            </div>
                            <a href="{{ route('procurement.po.index', ['view' => 'vendor']) }}" class="text-[10px] font-black text-amber-800 dark:text-amber-400 uppercase bg-amber-100 dark:bg-amber-900/40 px-3 py-1.5 rounded-lg hover:bg-amber-200 transition-colors">
                                Clear Filter
                            </a>
                        </div>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PO Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Buyer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($vendorPOs as $po)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 leading-none">{{ $po->po_number }}</span>
                                        @if($po->purchaseRequisition)
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">PR: {{ $po->purchaseRequisition->pr_number }}</div>
                                        @else
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 uppercase">Standalone / History</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 leading-none">
                                        {{ $po->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white leading-none">{{ $po->purchaseRequisition->company->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white leading-none">
                                        {{ $po->formatted_total_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="px-2 py-1 text-[10px] w-fit font-black rounded-full uppercase tracking-wider
                                                @if($po->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($po->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                                @elseif($po->status === 'issued') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                                {{ str_replace('_', ' ', $po->status) }}
                                            </span>
                                            
                                            @if($po->status === 'full_delivery' && $po->invoices_count === 0)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 dark:text-red-400 animate-pulse">
                                                    <i data-feather="alert-circle" class="w-3 h-3"></i> Invoice Required
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('procurement.po.show', $po) }}" class="inline-flex items-center text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1.5 rounded-lg transition-colors">
                                            View Details <i data-feather="chevron-right" class="w-4 h-4 ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <i data-feather="truck" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                                        <p class="font-bold">No customer orders found</p>
                                        <p class="text-xs mt-1">Orders from your customers will appear here.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($vendorPOs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $vendorPOs->appends(['view' => 'vendor'])->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Import History Modal --}}
    <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-md transition-opacity z-0" aria-hidden="true" onclick="closeImportModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block relative z-10 align-middle bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-gray-700">
                {{-- Step 1: Upload --}}
                <div id="importStepUpload">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 sm:mx-0 sm:h-10 sm:w-10 text-center">
                                    <i data-feather="upload-cloud" class="w-6 h-6"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">
                                        Import PO History
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Upload an Excel file (.xlsx) containing your historical Purchase Orders.
                                        </p>
                                    </div>
                                    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800 rounded-xl">
                                        <label class="block text-sm font-bold text-amber-800 dark:text-amber-400 mb-2">Import Role Classification</label>
                                        <div class="flex flex-col sm:flex-row gap-4">
                                            <label class="inline-flex items-center cursor-pointer group">
                                                <input type="radio" name="import_role" value="buyer" checked class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors">My Purchases (Buyer)</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer group">
                                                <input type="radio" name="import_role" value="vendor" class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 transition-colors">My Sales (Vendor)</span>
                                            </label>
                                        </div>
                                        <p class="mt-2 text-[10px] text-amber-600/70 dark:text-amber-400/50 italic">
                                            * Choose 'Buyer' if these are orders you SENT to vendors. Choose 'Vendor' if these are orders you RECEIVED from customers.
                                        </p>
                                    </div>
                                    <div class="mt-6">
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Select File</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-xl hover:border-primary-500 dark:hover:border-primary-500 transition-colors bg-gray-50 dark:bg-gray-700/50">
                                            <div class="space-y-1 text-center">
                                                <i data-feather="file" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-2"></i>
                                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                    <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-bold text-primary-600 dark:text-primary-400 hover:text-primary-500 focus-within:outline-none px-2 py-0.5">
                                                        <span>Upload a file</span>
                                                        <input id="file-upload" name="file" type="file" class="sr-only" required accept=".xlsx,.xls,.csv">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="file-name-display" class="mt-2 text-sm text-primary-600 dark:text-primary-400 font-bold text-center"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                            <button type="submit" id="btnPreview" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none transition shadow-lg shadow-primary-500/30 disabled:opacity-50">
                                <span id="previewText">Analyze File</span>
                                <span id="previewLoading" class="hidden animate-spin ml-2">
                                    <i data-feather="loader" class="w-4 h-4"></i>
                                </span>
                            </button>
                            <button type="button" onclick="closeImportModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors uppercase tracking-widest">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Step 2: Preview --}}
                <div id="importStepPreview" class="hidden">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white mb-4">
                            Import Preview
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Showing first 20 rows of <span id="totalRowsCount" class="font-bold"></span> total records found.
                        </p>
                        
                        <div class="overflow-x-auto max-h-[400px] border border-gray-100 dark:border-gray-700 rounded-xl">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead id="previewHeader" class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                                    {{-- Headers will be injected --}}
                                </thead>
                                <tbody id="previewBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                    {{-- Rows will be injected --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <form action="{{ route('procurement.po.confirm-import') }}" method="POST">
                        @csrf
                        <input type="hidden" name="temp_path" id="tempPathInput">
                        <input type="hidden" name="import_role" id="importRoleInput">
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none transition shadow-lg shadow-emerald-500/30">
                                Confirm & Start Import
                            </button>
                            <button type="button" onclick="backToUpload()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors uppercase tracking-widest">
                                Change File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            feather.replace();
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            backToUpload();
        }

        function backToUpload() {
            document.getElementById('importStepUpload').classList.remove('hidden');
            document.getElementById('importStepPreview').classList.add('hidden');
        }

        document.getElementById('file-upload')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                document.getElementById('file-name-display').textContent = 'Selected: ' + fileName;
            }
        });

        document.getElementById('uploadForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btn = document.getElementById('btnPreview');
            const spinning = document.getElementById('previewLoading');
            const text = document.getElementById('previewText');

            btn.disabled = true;
            spinning.classList.remove('hidden');
            text.textContent = 'Parsing...';
            feather.replace();

            fetch("{{ route('procurement.po.import-history') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderPreview(data);
                } else {
                    alert(data.message || 'Failed to parse file');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Analysis failed. Please check file format.');
            })
            .finally(() => {
                btn.disabled = false;
                spinning.classList.add('hidden');
                text.textContent = 'Analyze File';
                feather.replace();
            });
        });

        function renderPreview(data) {
            const headerRow = document.getElementById('previewHeader');
            const body = document.getElementById('previewBody');
            
            headerRow.innerHTML = '';
            body.innerHTML = '';

            if (data.preview.length > 0) {
                const keys = Object.keys(data.preview[0]);
                const trHeader = document.createElement('tr');
                keys.forEach(key => {
                    const th = document.createElement('th');
                    th.className = "px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider";
                    th.textContent = key.replace(/_/g, ' ');
                    trHeader.appendChild(th);
                });
                headerRow.appendChild(trHeader);

                data.preview.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-gray-50 dark:hover:bg-gray-700/50";
                    keys.forEach(key => {
                        const td = document.createElement('td');
                        td.className = "px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300";
                        td.textContent = row[key];
                        tr.appendChild(td);
                    });
                    body.appendChild(tr);
                });
            }

            document.getElementById('totalRowsCount').textContent = data.total_rows;
            document.getElementById('tempPathInput').value = data.temp_path;
            document.getElementById('importRoleInput').value = data.import_role;
            
            document.getElementById('importStepUpload').classList.add('hidden');
            document.getElementById('importStepPreview').classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endpush

