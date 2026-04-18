@extends('layouts.app', [
    'title' => 'Purchase Orders',
    'breadcrumbs' => [
        ['name' => 'Maps', 'url' => '#'],
        ['name' => 'Requisitions', 'url' => route('procurement.pr.index')],
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
                class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all bg-primary-600 text-white shadow-lg shadow-primary-600/20">
                Purchases
            </a>
            <a href="{{ route('procurement.po.index', ['view' => 'vendor']) }}"
                class="px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all text-gray-400 hover:bg-gray-50">
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

    @if($recentBuyerPOs->isNotEmpty())
        <div class="mb-10">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-6">Recent Activity</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($recentBuyerPOs as $po)
                    <a href="{{ route('procurement.po.show', $po) }}" 
                        class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-primary-600/5 transition-all group overflow-hidden">
                        <div class="flex flex-col h-full">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-2 py-1 bg-primary-100 text-primary-700 rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $po->po_number }}</span>
                                @if($po->created_at->gt(now()->subDay()))
                                    <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(37,99,235,0.6)]"></span>
                                @endif
                            </div>
                            <p class="text-[11px] font-black text-gray-900 dark:text-white truncate mb-1 uppercase tracking-tight">{{ $po->vendorCompany->name ?? 'N/A' }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-6">{{ $po->created_at->diffForHumans() }}</p>
                            
                            <div class="mt-auto flex items-end justify-between">
                                <div>
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">TOTAL</p>
                                    <p class="text-sm font-black text-gray-900 dark:text-white">{{ $po->formatted_total_amount }}</p>
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

    {{-- Buyer POs Tab --}}
    <div id="buyerContent">
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-primary-50/50 dark:bg-primary-900/10">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Active Purchases</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Orders you have issued to various vendors</p>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-700">
                    <thead class="bg-gray-50/50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">PO Header</th>
                            <th scope="col" class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Issued To</th>
                            <th scope="col" class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Financials</th>
                            <th scope="col" class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Fulfillment</th>
                            <th scope="col" class="px-8 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($buyerPOs as $po)
                            <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                <td class="px-8 py-5">
                                    <div class="text-[11px] font-black text-primary-600 uppercase tracking-widest">{{ $po->po_number }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">DATE: {{ $po->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $po->vendorCompany->name ?? 'N/A' }}</div>
                                    @if($po->purchaseRequisition)
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">REF: {{ $po->purchaseRequisition->pr_number }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-[11px] font-black text-gray-900 dark:text-white">{{ $po->formatted_total_amount }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">PAID IN ESCROW</div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($po->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">COMPLETED</span>
                                    @elseif($po->status === 'cancelled')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">CANCELLED</span>
                                    @elseif($po->status === 'issued')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">ISSUED</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-yellow-50 text-yellow-600 border border-yellow-100">{{ strtoupper(str_replace('_', ' ', $po->status)) }}</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('procurement.po.show', $po) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-gray-400 hover:text-primary-600 transition-all uppercase tracking-widest">
                                        TRACK PO
                                        <i data-feather="arrow-right" class="w-3 h-3"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-700">
                                        <i data-feather="package" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">No Purchase Orders</h3>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.2em] mt-1">Issue orders from awarded Requisitions</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile List View -->
            <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($buyerPOs as $po)
                    <div x-data="{ expanded: false }" class="p-4 bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $po->po_number }}</span>
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
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $po->vendorCompany->name ?? 'N/A' }}</p>
                                <p class="text-xs font-black text-gray-700 dark:text-gray-300">{{ $po->formatted_total_amount }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="expanded = !expanded" class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                    <i x-show="!expanded" data-feather="chevron-down" class="w-5 h-5"></i>
                                    <i x-show="expanded" data-feather="chevron-up" class="w-5 h-5" x-cloak></i>
                                </button>
                                <a href="{{ route('procurement.po.show', $po) }}" class="p-2 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-lg">
                                    <i data-feather="eye" class="w-5 h-5"></i>
                                </a>
                            </div>
                        </div>

                        <div x-show="expanded" x-collapse x-cloak class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-700/50">
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
                        <i data-feather="shopping-bag" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                        <p class="font-bold">No purchase orders found</p>
                    </div>
                @endforelse
            </div>
            
            @if($buyerPOs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $buyerPOs->appends(['view' => 'buyer'])->links() }}
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
