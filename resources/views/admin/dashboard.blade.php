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

    {{-- Analytics Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        {{-- Recent Tender Activity --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                    <i data-feather="activity" class="w-5 h-5 mr-2 text-primary-600"></i>
                    Recent Tender Activity
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Tender / PR</th>
                            <th class="px-6 py-3 font-semibold">Buyer</th>
                            <th class="px-6 py-3 font-semibold">Winner</th>
                            <th class="px-6 py-3 font-semibold text-right">Amount</th>
                            <th class="px-6 py-3 font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentTenders as $pr)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $pr->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $pr->pr_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ substr($pr->company->name, 0, 1) }}
                                        </div>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $pr->company->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($pr->winningOffer)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded bg-green-100 flex items-center justify-center text-xs font-bold text-green-700">
                                                {{ substr($pr->winningOffer->company->name, 0, 1) }}
                                            </div>
                                            <span class="text-green-700 dark:text-green-400 font-medium">{{ $pr->winningOffer->company->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">No Winner</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">
                                    {{ $pr->winningOffer ? $pr->winningOffer->formatted_total_price : '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $pr->updated_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No recent tender activity found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Purchased Products --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                    <i data-feather="trending-up" class="w-5 h-5 mr-2 text-green-600"></i>
                    Top Purchased Products
                </h3>
            </div>
            <div class="p-4">
                <div class="space-y-4">
                    @forelse($topProducts as $index => $product)
                        <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-500 dark:text-gray-400">
                                #{{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $product->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->transaction_count }}x</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Sold</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No sales data yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mt-8">
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