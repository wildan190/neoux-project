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
        <div class="hidden md:block text-right">
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs italic">
                {{ $currentView === 'vendor' ? 'Manage orders from your customers, confirm availability, and issue invoices.' : 'Review orders sent to vendors, track deliveries, and manage received goods.' }}
            </p>
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
                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">PR: {{ $po->purchaseRequisition->pr_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 leading-none">
                                        {{ $po->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white leading-none">{{ $po->vendorCompany->name }}</div>
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
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Orders from Customers</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">New or ongoing orders sent by your clients</p>
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
                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">PR: {{ $po->purchaseRequisition->pr_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 leading-none">
                                        {{ $po->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white leading-none">{{ $po->purchaseRequisition->company->name }}</div>
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
                
                @if($vendorPOs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $vendorPOs->appends(['view' => 'vendor'])->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endpush
