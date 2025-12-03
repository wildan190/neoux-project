@extends('admin.layouts.app', [
    'title' => 'Company Management',
    'breadcrumbs' => [
        ['label' => 'Companies']
    ]
])

@section('content')
    {{-- Header & Filters --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Companies</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and review company registrations</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.companies.index', ['status' => 'all']) }}"
                class="px-4 py-2 rounded-lg font-medium text-sm transition-all {{ $status === 'all' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                All
            </a>
            <a href="{{ route('admin.companies.index', ['status' => 'pending']) }}"
                class="px-4 py-2 rounded-lg font-medium text-sm transition-all {{ $status === 'pending' ? 'bg-yellow-600 text-white shadow-lg shadow-yellow-500/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                Pending
            </a>
            <a href="{{ route('admin.companies.index', ['status' => 'active']) }}"
                class="px-4 py-2 rounded-lg font-medium text-sm transition-all {{ $status === 'active' ? 'bg-green-600 text-white shadow-lg shadow-green-500/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                Active
            </a>
            <a href="{{ route('admin.companies.index', ['status' => 'declined']) }}"
                class="px-4 py-2 rounded-lg font-medium text-sm transition-all {{ $status === 'declined' ? 'bg-red-600 text-white shadow-lg shadow-red-500/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                Declined
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Company
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Owner
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($companies as $company)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                        {{ substr($company->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $company->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $company->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $company->user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ ucfirst($company->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($company->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                    @elseif($company->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                    @endif">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $company->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.companies.show', $company) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-xs font-medium">
                                    <i data-feather="eye" class="w-3 h-3 mr-1"></i>
                                    View
                                </a>
                                @if($company->status === 'pending')
                                    <form action="{{ route('admin.companies.approve', $company) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                            <i data-feather="check" class="w-3 h-3 mr-1"></i>
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.companies.decline', $company) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs font-medium">
                                            <i data-feather="x" class="w-3 h-3 mr-1"></i>
                                            Decline
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <i data-feather="inbox" class="w-8 h-8 text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">No companies found</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($companies->hasPages())
        <div class="mt-6">
            {{ $companies->links() }}
        </div>
    @endif
@endsection