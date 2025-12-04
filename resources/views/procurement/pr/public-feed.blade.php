@extends('layouts.app', [
    'title' => 'All Requests',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'All Requests', 'url' => '#']
    ]
])

@section('content')
    <div class="max-w-5xl mx-auto space-y-4">
        @forelse($requisitions as $pr)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                {{-- Header: Company + User Info --}}
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-start gap-3">
                        {{-- Company Name (Primary), User Name, Time --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $pr->company ? $pr->company->name : 'N/A' }}</p>
                                <span class="px-2 py-0.5 text-xs font-bold rounded-full 
                                    @if($pr->status === 'approved') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($pr->status === 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                    @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                    {{ ucfirst($pr->status) }}
                                </span>
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
                    <p class="text-lg font-bold text-gray-900 dark:text-white">No purchase requisitions yet</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Be the first to create one!</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($requisitions->hasPages())
        <div class="max-w-5xl mx-auto mt-6">
            {{ $requisitions->links() }}
        </div>
    @endif
@endsection
