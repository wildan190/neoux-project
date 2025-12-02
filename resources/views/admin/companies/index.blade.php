@extends('admin.layouts.app', [
    'title' => 'Company Management',
    'breadcrumbs' => [
        ['label' => 'Companies']
    ]
])

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Companies</h2>

        <div class="flex space-x-2">
            <a href="{{ route('admin.companies.index', ['status' => 'all']) }}"
                class="px-4 py-2 rounded-lg {{ $status === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                All
            </a>
            <a href="{{ route('admin.companies.index', ['status' => 'pending']) }}"
                class="px-4 py-2 rounded-lg {{ $status === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                Pending
            </a>
            <a href="{{ route('admin.companies.index', ['status' => 'active']) }}"
                class="px-4 py-2 rounded-lg {{ $status === 'active' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                Active
            </a>
            <a href="{{ route('admin.companies.index', ['status' => 'declined']) }}"
                class="px-4 py-2 rounded-lg {{ $status === 'declined' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                Declined
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Company</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Owner</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Category</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Status</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Created</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($companies as $company)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $company->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $company->user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucfirst($company->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                    @if($company->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($company->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                {{ ucfirst($company->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $company->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('admin.companies.show', $company) }}"
                                class="text-indigo-600 hover:text-indigo-900">View</a>
                            @if($company->status === 'pending')
                                <form action="{{ route('admin.companies.approve', $company) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                </form>
                                <form action="{{ route('admin.companies.decline', $company) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">Decline</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No companies found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $companies->links() }}
    </div>
@endsection