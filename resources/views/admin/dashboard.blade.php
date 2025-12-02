@extends('admin.layouts.app', [
    'title' => 'Dashboard',
    'breadcrumbs' => []
])

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Companies -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i data-feather="briefcase" class="w-12 h-12 text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Companies</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalCompanies }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Companies -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i data-feather="clock" class="w-12 h-12 text-yellow-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Approval</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $pendingCompanies }}</p>
                </div>
            </div>
        </div>

        <!-- Active Companies -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i data-feather="check-circle" class="w-12 h-12 text-green-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $activeCompanies }}</p>
                </div>
            </div>
        </div>

        <!-- Declined Companies -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i data-feather="x-circle" class="w-12 h-12 text-red-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Declined</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $declinedCompanies }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.companies.index', ['status' => 'pending']) }}"
                class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition">
                <i data-feather="clock" class="w-6 h-6 text-yellow-600 mr-3"></i>
                <span class="text-yellow-800 dark:text-yellow-200 font-medium">Review Pending Companies</span>
            </a>
            <a href="{{ route('admin.users.create') }}"
                class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                <i data-feather="user-plus" class="w-6 h-6 text-blue-600 mr-3"></i>
                <span class="text-blue-800 dark:text-blue-200 font-medium">Create New User</span>
            </a>
            <a href="{{ route('admin.admins.create') }}"
                class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                <i data-feather="shield" class="w-6 h-6 text-purple-600 mr-3"></i>
                <span class="text-purple-800 dark:text-purple-200 font-medium">Create New Admin</span>
            </a>
        </div>
    </div>
@endsection