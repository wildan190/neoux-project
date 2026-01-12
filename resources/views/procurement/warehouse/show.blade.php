@extends('layouts.app', [
    'title' => 'Warehouse Details',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Warehouse Management', 'url' => route('procurement.warehouse.index')],
        ['name' => $warehouse->name, 'url' => null]
    ]
])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $warehouse->name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Code: {{ $warehouse->code }} • 
                <span class="@if($warehouse->is_active) text-green-600 @else text-red-600 @endif font-semibold">
                    {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
            @if($warehouse->address)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><i data-feather="map-pin" class="w-4 h-4 inline mr-1"></i> {{ $warehouse->address }}</p>
            @endif
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex flex-wrap gap-2">
            <a href="{{ route('procurement.warehouse.edit', $warehouse) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:w-auto">
                <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Warehouse
            </a>
            <a href="{{ route('procurement.warehouse.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:w-auto">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="package" class="h-6 w-6 text-primary-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Items Type</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $warehouse->stocks->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="layers" class="h-6 w-6 text-primary-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Quantity</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $warehouse->stocks->sum('quantity') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock Inventory Table --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Current Stock Inventory</h3>
            <span class="text-xs text-gray-500">Real-time stock levels at this location</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Updated</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($warehouse->stocks as $stock)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $stock->catalogueItem->name ?? 'Unknown Item' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $stock->catalogueItem->sku ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold @if($stock->quantity <= 0) text-red-600 @else text-green-600 @endif">
                                    {{ $stock->quantity }}
                                </span> 
                                <span class="text-xs text-gray-500">{{ $stock->catalogueItem->unit ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $stock->updated_at->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="mx-auto h-12 w-12 text-gray-400">
                                    <i data-feather="inbox" class="w-full h-full"></i>
                                </div>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No items in this warehouse</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Received goods will appear here automatically.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush
