@extends('layouts.app', [
    'title' => 'Purchase Requisitions',
    'breadcrumbs' => [
        ['name' => 'Shopping', 'url' => route('procurement.marketplace.index')],
        ['name' => 'Company PR', 'url' => '#']
    ]
])

@section('content')
    {{-- Simplified Filter Tabs --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="inline-flex p-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl">
            <a href="{{ route('procurement.pr.index', ['filter' => 'open']) }}"
                class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200
                    {{ $filter === 'open' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-50' }}">
                Open ({{ $openCount }})
            </a>

            <a href="{{ route('procurement.pr.index', ['filter' => 'closed']) }}"
                class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200
                    {{ $filter === 'closed' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-50' }}">
                Closed ({{ $closedCount }})
            </a>

            <a href="{{ route('procurement.pr.index', ['filter' => 'all']) }}"
                class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200
                    {{ $filter === 'all' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-50' }}">
                All
            </a>
        </div>

        <a href="{{ route('procurement.pr.create') }}"
            class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 dark:bg-primary-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-900/10">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Create New Request
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                    <tr>
                        <th scope="col" class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Request Details</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Requester</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Date</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Scope</th>
                        <th scope="col" class="px-8 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($requisitions as $pr)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all duration-200">
                            <td class="px-8 py-5">
                                <div class="text-sm font-black text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">{{ $pr->title }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">{{ Str::limit($pr->description, 40) ?: 'NO DESCRIPTION' }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[10px] font-black text-gray-500 overflow-hidden shadow-sm">
                                        @if($pr->user->userDetail && $pr->user->userDetail->profile_photo_url)
                                            <img class="w-full h-full object-cover" src="{{ $pr->user->userDetail->profile_photo_url }}" alt="{{ $pr->user->name }}">
                                        @else
                                            {{ substr($pr->user->name, 0, 2) }}
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-bold text-gray-600 dark:text-gray-300">{{ explode(' ', $pr->user->name)[0] }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-[11px] font-bold text-gray-500 uppercase tracking-tighter">
                                {{ $pr->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-5">
                                @if(in_array($pr->status, ['awarded', 'ordered']))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">
                                        {{ $pr->status }}
                                    </span>
                                @elseif($pr->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                        REJECTED
                                    </span>
                                @elseif(str_starts_with($pr->approval_status, 'pending'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-yellow-50 text-yellow-600 border border-yellow-100">
                                        PENDING
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-primary-50 text-primary-600 border border-primary-100">
                                        {{ $pr->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-[11px] font-black text-gray-900 dark:text-white">{{ $pr->items->count() }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Items</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="{{ route('procurement.pr.show', $pr) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-gray-400 hover:text-primary-600 transition-all uppercase tracking-widest">
                                    DETAILS
                                    <i data-feather="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-700">
                                    <i data-feather="file-text" class="w-6 h-6 text-gray-300"></i>
                                </div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">No Requisitions</h3>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.2em] mt-1">Start by creating your first purchase request</p>
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
                    <div x-show="expanded" x-cloak class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-700/50">
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
