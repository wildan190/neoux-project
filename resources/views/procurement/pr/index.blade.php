@extends('layouts.app', [
    'title' => 'Purchase Requisitions',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'All Requests', 'url' => '#']
    ]
])

@section('content')
    {{-- Filter Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-1.5 mb-6">
        <div class="grid grid-cols-3 gap-1">
            <a href="{{ route('procurement.pr.index', ['filter' => 'open']) }}"
                class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 px-2 py-3 rounded-xl text-[11px] font-bold transition-all duration-200
                    {{ $filter === 'open' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <i data-feather="unlock" class="w-3.5 h-3.5"></i>
                <span>Open</span>
                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold
                    {{ $filter === 'open' ? 'bg-white/20 text-white' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                    {{ $openCount }}
                </span>
            </a>

            <a href="{{ route('procurement.pr.index', ['filter' => 'closed']) }}"
                class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 px-2 py-3 rounded-xl text-[11px] font-bold transition-all duration-200
                    {{ $filter === 'closed' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <i data-feather="lock" class="w-3.5 h-3.5"></i>
                <span>Closed</span>
                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold
                    {{ $filter === 'closed' ? 'bg-white/20 text-white' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                    {{ $closedCount }}
                </span>
            </a>

            <a href="{{ route('procurement.pr.index', ['filter' => 'all']) }}"
                class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 px-2 py-3 rounded-xl text-[11px] font-bold transition-all duration-200
                    {{ $filter === 'all' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <i data-feather="list" class="w-3.5 h-3.5"></i>
                <span>All</span>
                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold
                    {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $openCount + $closedCount }}
                </span>
            </a>
        </div>
    </div>

    <div class="flex justify-end mb-6">
        <a href="{{ route('procurement.pr.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Create Request
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Requested By</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Items</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">View</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($requisitions as $pr)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $pr->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($pr->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($pr->user->userDetail && $pr->user->userDetail->profile_photo_url)
                                            <img class="h-8 w-8 rounded-lg object-cover" src="{{ $pr->user->userDetail->profile_photo_url }}" alt="{{ $pr->user->name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-xs">
                                                {{ substr($pr->user->name, 0, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $pr->user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pr->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $pr->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(in_array($pr->status, ['awarded', 'ordered']))
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        {{ ucfirst($pr->status) }}
                                    </span>
                                @elseif($pr->status === 'rejected')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        {{ ucfirst($pr->status) }}
                                    </span>
                                @elseif(str_starts_with($pr->approval_status, 'pending'))
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        Pending
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ ucfirst($pr->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $pr->items->count() }}</span> items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('procurement.pr.show', $pr) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 font-bold uppercase tracking-wider text-[10px] bg-primary-50 dark:bg-primary-900/20 px-3 py-1.5 rounded-lg transition-all">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <i data-feather="shopping-cart" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                                <p class="font-bold">No requisitions found</p>
                                <p class="text-xs mt-1">Try changing your filters or create a new request.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Card List) -->
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($requisitions as $pr)
                <div x-data="{ expanded: false }" class="p-4 bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex flex-col min-w-0">
                            <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $pr->title }}</span>
                            <span class="text-[10px] text-gray-500 font-medium">{{ $pr->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex-shrink-0">
                            @if(in_array($pr->status, ['awarded', 'ordered']))
                                <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 uppercase tracking-tight">
                                    {{ $pr->status }}
                                </span>
                            @elseif($pr->status === 'rejected')
                                <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 uppercase tracking-tight">
                                    {{ $pr->status }}
                                </span>
                            @elseif(str_starts_with($pr->approval_status, 'pending'))
                                <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 uppercase tracking-tight">
                                    Pending
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 uppercase tracking-tight">
                                    {{ $pr->status }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0 h-6 w-6">
                                @if($pr->user->userDetail && $pr->user->userDetail->profile_photo_url)
                                    <img class="h-6 w-6 rounded-lg object-cover" src="{{ $pr->user->userDetail->profile_photo_url }}" alt="{{ $pr->user->name }}">
                                @else
                                    <div class="h-6 w-6 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-[8px]">
                                        {{ substr($pr->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <span class="ml-2 text-xs text-gray-600 dark:text-gray-400 truncate">{{ $pr->user->name }}</span>
                            <span class="mx-2 text-gray-300 dark:text-gray-700 text-xs">•</span>
                            <span class="text-[10px] font-bold text-gray-900 dark:text-white">{{ $pr->items->count() }} items</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="expanded = !expanded" class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500">
                                <i x-show="!expanded" data-feather="chevron-down" class="w-5 h-5"></i>
                                <i x-show="expanded" data-feather="chevron-up" class="w-5 h-5" x-cloak></i>
                            </button>
                            <a href="{{ route('procurement.pr.show', $pr) }}" class="p-2 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-lg">
                                <i data-feather="eye" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Expandable Context -->
                    <div x-show="expanded" x-collapse x-cloak class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-700/50">
                        <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                            {{ $pr->description ?: 'No description provided.' }}
                        </p>
                        <div class="mt-2 flex gap-1 pt-1">
                             <a href="{{ route('procurement.pr.show', $pr) }}" class="text-[10px] font-bold text-primary-600 hover:underline">View Details →</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <i data-feather="shopping-cart" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600"></i>
                    <p class="font-bold">No requisitions found</p>
                </div>
            @endforelse
        </div>
    </div>
    
    @if($requisitions->hasPages())
        <div class="mt-6">
            {{ $requisitions->links() }}
        </div>
    @endif
@endsection
