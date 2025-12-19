@extends('layouts.app', [
    'title' => 'Warehouse Report',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Warehouse', 'url' => route('warehouse.index')],
        ['name' => 'Report', 'url' => '#']
    ]
])

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Warehouse Activity Report</h2>
            <p class="text-gray-600 dark:text-gray-400">Track stock movements and inventory history</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 text-green-600 rounded-lg dark:bg-green-900/30 dark:text-green-400">
                    <i data-feather="arrow-down-left" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Items IN (Today)</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">+{{ $todayIn }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-100 text-red-600 rounded-lg dark:bg-red-900/30 dark:text-red-400">
                    <i data-feather="arrow-up-right" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Items OUT (Today)</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">-{{ $todayOut }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white">Recent Movements</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 font-medium">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Item</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3 text-right">Qty</th>
                            <th class="px-6 py-3 text-right">Stock</th>
                            <th class="px-6 py-3">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($movements as $movement)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $movement->created_at->format('d M H:i') }}
                                </td>
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $movement->item->product->name ?? 'Unknown' }}
                                    <span class="block text-xs text-gray-500 font-normal">{{ $movement->item->sku }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    @if($movement->type === 'in')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            IN
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            OUT
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right font-medium {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                                </td>
                                <td class="px-6 py-3 text-right text-gray-500 dark:text-gray-400">
                                    {{ $movement->current_stock }}
                                </td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $movement->user->name ?? 'System' }}
                                    @if($movement->notes)
                                        <span class="block italic mt-1" title="{{ $movement->notes }}">Note: {{ Str::limit($movement->notes, 20) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No movements recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $movements->links() }}
            </div>
        </div>

        {{-- Top Moved --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 h-fit">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white">Top Active Items</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($topItems as $top)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $top->item->product->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $top->item->sku }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-sm font-bold text-primary-600">{{ $top->total_qty }} Qty</span>
                            <span class="text-xs text-gray-400">{{ $top->count }} Moves</span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                        No data yet
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endsection
