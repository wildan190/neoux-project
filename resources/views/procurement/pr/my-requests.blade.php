@extends('layouts.app', [
    'title' => 'My Requests',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'My Requests', 'url' => '#']
    ]
])

@section('content')
    <div class="flex justify-end mb-6">
        <a href="{{ route('procurement.pr.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Create Request
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 p-4 mb-6 border border-green-100 dark:border-green-900/30">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-feather="check-circle" class="h-5 w-5 text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Company</th>
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
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if($pr->company && $pr->company->logo_url)
                                        <img class="h-10 w-16 rounded border border-gray-200 dark:border-gray-600 object-contain bg-white dark:bg-gray-800 p-1" src="{{ $pr->company->logo_url }}" alt="{{ $pr->company->name }}">
                                    @else
                                        <div class="h-10 w-16 rounded border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex items-center justify-center">
                                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500">{{ $pr->company ? strtoupper(substr($pr->company->name, 0, 3)) : 'CO' }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $pr->company ? $pr->company->name : 'N/A' }}</div>
                                    @if($pr->company && $pr->company->category)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($pr->company->category) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $pr->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                                @if($pr->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($pr->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                {{ ucfirst($pr->status) }}
                            </span>
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i data-feather="inbox" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">No requests found</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Start by creating your first request!</p>
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
