@extends('layouts.app', [
    'title' => 'Warehouse Management',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Warehouse', 'url' => route('warehouse.index')]
    ]
])

@section('content')
<div class="space-y-6">
    {{-- Header & Actions --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Warehouse Dashboard</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Manage inventory, scan items, and track stock.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('warehouse.scan') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:w-auto">
                <i data-feather="maximize" class="w-4 h-4 mr-2"></i> Scan QR
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="package" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $totalItems }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i data-feather="layers" class="h-6 w-6 text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Stock</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $totalStock }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Low Stock & Recent --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Low Stock Alert --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-red-600 dark:text-red-400 flex items-center">
                    <i data-feather="alert-circle" class="mr-2 w-5 h-5"></i> Low Stock Alert
                </h3>
            </div>
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($lowStockItems as $item)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-primary-600 truncate">
                            {{ $item->product->name }} ({{ $item->sku }})
                        </div>
                        <div class="ml-2 flex-shrink-0 flex">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                Stock: {{ $item->stock }} {{ $item->unit }}
                            </span>
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-4 py-4 sm:px-6 text-gray-500 dark:text-gray-400 text-sm">No items low on stock.</li>
                @endforelse
            </ul>
        </div>

        {{-- Recent Activity / Items --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    Recently Updated Items
                </h3>
            </div>
             <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentItems as $item)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                             {{ $item->product->name }} 
                        </div>
                        <div class="ml-2 flex-shrink-0 flex flex-col items-end">
                            <span class="text-sm text-gray-500">{{ $item->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                     <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->sku }} - Qty: {{ $item->stock }}</p>
                </li>
                @empty
                 <li class="px-4 py-4 sm:px-6 text-gray-500 dark:text-gray-400 text-sm">No recent items.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
