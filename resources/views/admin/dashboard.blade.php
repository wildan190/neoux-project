@extends('admin.layouts.app', [
    'title' => 'Dashboard',
    'breadcrumbs' => []
])

@section('content')
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        {{-- Total Companies --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                        <i data-feather="briefcase" class="w-7 h-7 text-white"></i>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Companies</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCompanies }}</p>
            </div>
        </div>

        {{-- Pending Approval --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                        <i data-feather="clock" class="w-7 h-7 text-white"></i>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Pending Approval</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingCompanies }}</p>
            </div>
        </div>

        {{-- Active Companies --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                        <i data-feather="check-circle" class="w-7 h-7 text-white"></i>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Active</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $activeCompanies }}</p>
            </div>
        </div>

        {{-- Declined Companies --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                        <i data-feather="x-circle" class="w-7 h-7 text-white"></i>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Declined</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $declinedCompanies }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                <i data-feather="zap" class="w-5 h-5 mr-2 text-primary-600"></i>
                Quick Actions
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.companies.index', ['status' => 'pending']) }}"
                    class="group flex items-center gap-4 p-5 bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800/30 rounded-xl hover:bg-yellow-100 dark:hover:bg-yellow-900/20 transition-all shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <div class="w-12 h-12 rounded-lg bg-yellow-500 flex items-center justify-center flex-shrink-0 shadow-lg transform group-hover:scale-110 transition-transform">
                        <i data-feather="clock" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-yellow-900 dark:text-yellow-100 group-hover:text-yellow-700 dark:group-hover:text-yellow-200 transition-colors">Review Pending</p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300">{{ $pendingCompanies }} awaiting approval</p>
                    </div>
                </a>

                <a href="{{ route('admin.users.create') }}"
                    class="group flex items-center gap-4 p-5 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/20 transition-all shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <div class="w-12 h-12 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0 shadow-lg transform group-hover:scale-110 transition-transform">
                        <i data-feather="user-plus" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100 group-hover:text-blue-700 dark:group-hover:text-blue-200 transition-colors">Create User</p>
                        <p class="text-xs text-blue-700 dark:text-blue-300">Add a new user account</p>
                    </div>
                </a>

                <a href="{{ route('admin.admins.create') }}"
                    class="group flex items-center gap-4 p-5 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-800/30 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/20 transition-all shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <div class="w-12 h-12 rounded-lg bg-purple-500 flex items-center justify-center flex-shrink-0 shadow-lg transform group-hover:scale-110 transition-transform">
                        <i data-feather="shield" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-purple-900 dark:text-purple-100 group-hover:text-purple-700 dark:group-hover:text-purple-200 transition-colors">Create Admin</p>
                        <p class="text-xs text-purple-700 dark:text-purple-300">Add a new administrator</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection