@extends('layouts.app', [
    'title' => 'Purchase Orders',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => null],
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
                    <i data-feather="shopping-bag" class="w-4 h-4 inline mr-2"></i>
                    My Purchase Orders
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                        {{ $buyerPOs->total() }}
                    </span>
                </button>
                <button onclick="switchTab('vendor')" id="vendorTab"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                           border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    <i data-feather="truck" class="w-4 h-4 inline mr-2"></i>
                    Vendor Orders
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                        {{ $vendorPOs->total() }}
                    </span>
                </button>
            </nav>
        </div>
    </div>

    {{-- Buyer POs Tab --}}
    <div id="buyerContent" class="tab-content">
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-primary-50/50 dark:bg-primary-900/10">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">My Purchase Orders (as Buyer)</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Orders where you can receive goods and manage deliveries</p>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($buyerPOs as $po)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $po->po_number }}</span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">PR: {{ $po->purchaseRequisition->pr_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $po->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $po->vendorCompany->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $po->formatted_total_amount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full 
                                        @if($po->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($po->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($po->status === 'issued') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                        @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $po->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('procurement.po.show', $po) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <i data-feather="shopping-bag" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                                    <p>No purchase orders found where you are the buyer.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($buyerPOs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $buyerPOs->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Vendor POs Tab --}}
    <div id="vendorContent" class="tab-content hidden">
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-green-50/50 dark:bg-green-900/10">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Vendor Orders (as Vendor)</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Orders where you can create invoices and deliver goods</p>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($vendorPOs as $po)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $po->po_number }}</span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">PR: {{ $po->purchaseRequisition->pr_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $po->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $po->purchaseRequisition->company->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $po->formatted_total_amount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full 
                                        @if($po->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($po->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                        @elseif($po->status === 'issued') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                        @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $po->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('procurement.po.show', $po) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <i data-feather="truck" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                                    <p>No purchase orders found where you are the vendor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($vendorPOs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $vendorPOs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function switchTab(tab) {
            // Update tab buttons
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
