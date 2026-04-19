@extends('layouts.app', [
    'title' => 'Sales Orders',
    'breadcrumbs' => [
        ['name' => 'Maps', 'url' => '#'],
        ['name' => 'Sales', 'url' => route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => null],
    ]
])

@section('content')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="inline-flex p-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl">
            <a href="{{ route('procurement.po.index', ['view' => 'buyer']) }}"
                class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all text-gray-400 hover:bg-gray-50">
                Purchases
            </a>
            <a href="{{ route('procurement.po.index', ['view' => 'vendor']) }}"
                class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all bg-primary-600 text-white shadow-lg shadow-primary-600/20">
                Sales
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('procurement.po.export-template') }}" target="_blank"
                class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition-all">
                Export Template
            </a>
            <button type="button" onclick="openImportModal()"
                    class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all">
                Import History
            </button>
        </div>
    </div>

    @if($recentVendorPOs->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recent Orders</h3>
                <span class="text-[10px] text-gray-400 uppercase font-medium">Action Required</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($recentVendorPOs as $po)
                    <a href="{{ route('procurement.po.show', $po) }}" 
                        class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/30 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                        @if($po->created_at->gt(now()->subDay()))
                            <div class="absolute top-0 right-0">
                                <div class="bg-emerald-500 text-white text-[8px] font-black px-3 py-1 rounded-bl-xl uppercase tracking-tighter">NEW</div>
                            </div>
                        @endif
                        <div class="flex flex-col h-full">
                            <div class="mb-2">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $po->po_number }}</span>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $po->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate mb-1">{{ $po->purchaseRequisition->company->name ?? 'N/A' }}</p>
                            <p class="text-xs font-black text-gray-700 dark:text-gray-300">{{ $po->formatted_total_amount }}</p>
                            <div class="mt-4 pt-3 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ str_replace('_', ' ', $po->status) }}</span>
                                <i data-feather="arrow-right" class="w-4 h-4 text-emerald-500 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Vendor POs Tab --}}
    <div id="vendorContent">
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-emerald-50/50 dark:bg-emerald-900/10">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Orders from Customers</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">New or ongoing orders sent by your clients</p>
            </div>

            <div class="hidden md:block overflow-x-auto">
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
                                    <span class="px-2 py-1 text-[10px] font-black rounded-full uppercase tracking-wider
                                        @if($po->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($po->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($po->status === 'issued') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                        @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                        {{ str_replace('_', ' ', $po->status) }}
                                    </span>
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

            <!-- Mobile List View (Vendor) -->
            <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($vendorPOs as $po)
                    <div x-data="{ expanded: false }" class="p-4 bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $po->po_number }}</span>
                                <span class="text-[10px] text-gray-400 uppercase tracking-tight">{{ $po->created_at->format('d M Y') }}</span>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black rounded-full uppercase tracking-wider
                                @if($po->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($po->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                @elseif($po->status === 'issued') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                {{ str_replace('_', ' ', $po->status) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $po->purchaseRequisition->company->name ?? 'N/A' }}</p>
                                <p class="text-xs font-black text-gray-700 dark:text-gray-300">{{ $po->formatted_total_amount }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="expanded = !expanded" class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                    <i x-show="!expanded" data-feather="chevron-down" class="w-5 h-5"></i>
                                    <i x-show="expanded" data-feather="chevron-up" class="w-5 h-5" x-cloak></i>
                                </button>
                                <a href="{{ route('procurement.po.show', $po) }}" class="p-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg">
                                    <i data-feather="eye" class="w-5 h-5"></i>
                                </a>
                            </div>
                        </div>

                        <div x-show="expanded" x-cloak class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="grid grid-cols-2 gap-y-3">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-black mb-0.5">Reference PR</p>
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $po->purchaseRequisition->pr_number ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-black mb-0.5">Created By</p>
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $po->createdBy->name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <i data-feather="truck" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                        <p class="font-bold">No customer orders found</p>
                    </div>
                @endforelse
            </div>
            
            @if($vendorPOs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $vendorPOs->appends(['view' => 'vendor'])->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Common Modals --}}
    @include('procurement::partials.po_import_modal')
@endsection

@push('scripts')
    @include('procurement::partials.po_index_scripts')
@endpush
