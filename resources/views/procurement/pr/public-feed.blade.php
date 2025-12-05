@extends('layouts.app', [
    'title' => 'All Requests',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'All Requests', 'url' => '#']
    ]
])

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Filter Tabs --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('procurement.pr.public-feed', ['filter' => 'open']) }}"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200
                        {{ $filter === 'open' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i data-feather="inbox" class="w-4 h-4"></i>
                    <span>Open Requests</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $filter === 'open' ? 'bg-white/20 text-white' : 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' }}">
                        {{ $openCount }}
                    </span>
                </a>

                <a href="{{ route('procurement.pr.public-feed', ['filter' => 'closed']) }}"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200
                        {{ $filter === 'closed' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i data-feather="check-circle" class="w-4 h-4"></i>
                    <span>Closed</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $filter === 'closed' ? 'bg-white/20 text-white' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                        {{ $closedCount }}
                    </span>
                </a>

                <a href="{{ route('procurement.pr.public-feed', ['filter' => 'all']) }}"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200
                        {{ $filter === 'all' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i data-feather="list" class="w-4 h-4"></i>
                    <span>All</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ $openCount + $closedCount }}
                    </span>
                </a>
            </div>
        </div>

        {{-- Results Info --}}
        <div class="flex items-center justify-between px-1">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                @if($filter === 'open')
                    Showing <span class="font-bold text-gray-900 dark:text-white">{{ $requisitions->total() }}</span> open requests
                @elseif($filter === 'closed')
                    Showing <span class="font-bold text-gray-900 dark:text-white">{{ $requisitions->total() }}</span> closed requests
                @else
                    Showing <span class="font-bold text-gray-900 dark:text-white">{{ $requisitions->total() }}</span> all requests
                @endif
            </p>
        </div>

        {{-- Requests List --}}
        <div class="space-y-4">
            @forelse($requisitions as $pr)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                    {{-- Header: Company + User Info --}}
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-start justify-between gap-3">
                            {{-- Company Name (Primary), User Name, Time --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ $pr->company ? $pr->company->name : 'N/A' }}</p>
                                    @if($pr->status === 'pending')
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 flex items-center gap-1">
                                            <i data-feather="unlock" class="w-3 h-3"></i> OPEN
                                        </span>
                                    @elseif($pr->status === 'awarded')
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 flex items-center gap-1">
                                            <i data-feather="award" class="w-3 h-3"></i> AWARDED
                                        </span>
                                    @elseif($pr->status === 'ordered')
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 flex items-center gap-1">
                                            <i data-feather="check" class="w-3 h-3"></i> ORDERED
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $pr->user->name }}
                                        @if($pr->company && $pr->company->category)
                                            • {{ ucfirst($pr->company->category) }}
                                        @endif
                                    </p>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $pr->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-4">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $pr->title }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($pr->description ?: 'No description provided', 150) }}</p>
                        
                        {{-- Preview Stats --}}
                        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-3">
                            <div class="flex items-center gap-1">
                                <i data-feather="shopping-cart" class="w-3.5 h-3.5"></i>
                                <span>{{ $pr->items->count() }} items</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i data-feather="message-circle" class="w-3.5 h-3.5"></i>
                                <span>{{ $pr->comments->count() }} comments</span>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('procurement.pr.show-public', $pr) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-lg transition w-full justify-center">
                            View Details
                            <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col items-center justify-center">
                        <i data-feather="inbox" class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4"></i>
                        @if($filter === 'open')
                            <p class="text-lg font-bold text-gray-900 dark:text-white">No open requests</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">All tenders have been closed</p>
                        @elseif($filter === 'closed')
                            <p class="text-lg font-bold text-gray-900 dark:text-white">No closed requests</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No tenders have been awarded yet</p>
                        @else
                            <p class="text-lg font-bold text-gray-900 dark:text-white">No purchase requisitions yet</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Be the first to create one!</p>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($requisitions->hasPages())
        <div class="max-w-5xl mx-auto mt-6">
            {{ $requisitions->links() }}
        </div>
    @endif
@endsection
