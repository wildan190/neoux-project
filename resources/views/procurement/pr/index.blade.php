@extends('layouts.app', [
    'title' => 'Purchase Requisitions',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'All Requests', 'url' => '#']
    ]
])

@section('content')
    {{-- Filter Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-2 mb-6">
        <div class="flex items-center gap-2">
            <a href="{{ route('procurement.pr.index', ['filter' => 'open']) }}"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200
                    {{ $filter === 'open' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <i data-feather="unlock" class="w-4 h-4"></i>
                <span>Open</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                    {{ $filter === 'open' ? 'bg-white/20 text-white' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                    {{ $openCount }}
                </span>
            </a>

            <a href="{{ route('procurement.pr.index', ['filter' => 'closed']) }}"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-200
                    {{ $filter === 'closed' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                <i data-feather="lock" class="w-4 h-4"></i>
                <span>Closed</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                    {{ $filter === 'closed' ? 'bg-white/20 text-white' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                    {{ $closedCount }}
                </span>
            </a>

            <a href="{{ route('procurement.pr.index', ['filter' => 'all']) }}"
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

    <div class="flex justify-end mb-6">
        <a href="{{ route('procurement.pr.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Create Request
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
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
                            @else
                                {{-- Show Approval Status for open items --}}
                                @if($pr->approval_status === 'approved')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        Approved
                                    </span>
                                @elseif($pr->approval_status === 'rejected')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        Rejected
                                    </span>
                                @elseif(str_starts_with($pr->approval_status, 'pending'))
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        {{ str_replace('_', ' ', strtoupper($pr->approval_status)) }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Draft
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $pr->items->count() }} items
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('procurement.pr.show', $pr) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 font-semibold">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i data-feather="inbox" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">No purchase requisitions found</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">There are no requests in the system yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $requisitions->links() }}
    </div>
@endsection
