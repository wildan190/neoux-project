@extends('layouts.app', [
    'title' => 'Dashboard',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Dashboard', 'url' => '#']
    ]
])

@section('content')
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Dashboard Overview</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Selamat datang! Berikut ringkasan aktivitas <strong>{{ $company->name }}</strong> hari ini.
            </p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <span class="px-3 py-1.5 rounded-lg font-medium {{ $isBuyer ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                {{ $isBuyer ? '🛒 Buyer' : '🏪 Vendor' }}
            </span>
        </div>
    </div>

    {{-- WIDGET TOP --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @if($isBuyer)
            {{-- Category 1: Spend Analyst --}}
            <div class="lg:col-span-3 mt-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="pie-chart" class="w-5 h-5 text-blue-500"></i> Spend Analyst
                </h3>
            </div>
            
            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="dollar-sign" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Spend</p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format(($stats['spend_analyst']['total_spend'] ?? 0) / 1000000, 1) }}M</h2>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Maverick Spend</p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format(($stats['spend_analyst']['maverick_spend'] ?? 0) / 1000000, 1) }}M</h2>
                    <p class="text-[10px] text-gray-400 mt-1">Purchase without Requisitions</p>
                </div>
            </div>

            {{-- Spend by Supplier Breakdown --}}
            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Top 3 Suppliers</h4>
                <div class="space-y-3">
                    @foreach($stats['spend_analyst']['spend_by_supplier']->sortByDesc('total')->take(3) as $supplier)
                    <div>
                        <div class="flex justify-between items-center text-[10px] mb-1">
                            <span class="text-gray-500 truncate mr-2">{{ $supplier['name'] }}</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">Rp{{ number_format($supplier['total']/1000000, 1) }}M</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1">
                            @php $p = ($stats['spend_analyst']['total_spend'] > 0) ? ($supplier['total'] / $stats['spend_analyst']['total_spend'] * 100) : 0; @endphp
                            <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $p }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Category 2: Supplier Performance --}}
            <div class="lg:col-span-3 mt-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="award" class="w-5 h-5 text-green-500"></i> Supplier Performance
                </h3>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="clock" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Lead Time</p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['supplier_performance']['avg_lead_time'] ?? 0 }} Days</h2>
                    <p class="text-[10px] text-gray-400 mt-1">PO Acceptance to Goods Receipt</p>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="check-circle" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fill Rate</p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['supplier_performance']['fill_rate'] ?? 0 }}%</h2>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-2">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $stats['supplier_performance']['fill_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="hidden lg:block"></div> {{-- Spacer to keep grid balanced if needed, or we can leave it to flow --}}

            {{-- Category 3: Operational Efficiency --}}
            <div class="lg:col-span-3 mt-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="zap" class="w-5 h-5 text-orange-500"></i> Operational Efficiency
                </h3>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="activity" class="w-6 h-6 text-orange-600 dark:text-orange-400"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">PO Cycle Time</p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['operational_efficiency']['avg_cycle_time'] ?? 0 }} Days</h2>
                    <p class="text-[10px] text-gray-400 mt-1">PR Submission to PO Generation</p>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group flex flex-col justify-center">
                <p class="text-xs font-medium text-gray-500 mb-2">PO Status Active</p>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active_orders'] ?? 0 }}</h2>
                    <span class="text-[10px] text-orange-500 font-bold mb-1">Items Pending Approval</span>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group flex flex-col justify-center">
                <p class="text-xs font-medium text-gray-500 mb-2">PR Pending Approval</p>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_pr'] ?? 0 }}</h2>
                    <span class="text-[10px] text-blue-500 font-bold mb-1">Awaiting Review</span>
                </div>
            </div>

            {{-- Category 4: Cost Management --}}
            <div class="lg:col-span-3 mt-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="trending-down" class="w-5 h-5 text-purple-500"></i> Cost Management
                </h3>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="shield" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cost Savings</p>
                    <h2 class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format(($stats['cost_management']['cost_savings'] ?? 0) / 1000000, 1) }}M</h2>
                    <p class="text-[10px] text-gray-400 mt-1">Saving from Highest Quotation</p>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="bar-chart-2" class="w-6 h-6 text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Price Variance</p>
                    <h2 class="text-2xl font-bold {{ ($stats['cost_management']['ppV'] ?? 0) > 0 ? 'text-red-500' : 'text-green-500' }} mt-1">
                        Rp {{ number_format(abs(($stats['cost_management']['ppV'] ?? 0)) / 1000000, 1) }}M
                    </h2>
                    <p class="text-[10px] text-gray-400 mt-1">vs Average Quotation</p>
                </div>
            </div>
        @else
            {{-- Vendor Widgets --}}
            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="shopping-cart" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                        <i data-feather="trending-up" class="w-3 h-3"></i> {{ $stats['sales_change'] ?? '+0%' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Penjualan</p>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format(($stats['total_sales'] ?? 0) / 1000000, 1) }}M</h2>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-secondary-50 dark:bg-secondary-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="package" class="w-6 h-6 text-secondary-600 dark:text-secondary-400"></i>
                    </div>
                    <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                        <i data-feather="trending-up" class="w-3 h-3"></i> {{ $stats['orders_change'] ?? '+0%' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Order Aktif</p>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['active_orders'] ?? 0 }}</h2>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                        {{ $stats['total_invoices'] ?? 0 }} Invoice
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Invoice</p>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format(($stats['invoice_amount'] ?? 0) / 1000000, 1) }}M</h2>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="box" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <span class="text-xs font-bold {{ str_starts_with($stats['products_change'] ?? '0', '-') ? 'text-red-600 bg-red-50 dark:bg-red-900/20' : 'text-green-600 bg-green-50 dark:bg-green-900/20' }} px-2 py-1 rounded-lg flex items-center gap-1">
                        <i data-feather="{{ str_starts_with($stats['products_change'] ?? '0', '-') ? 'trending-down' : 'trending-up' }}" class="w-3 h-3"></i> {{ $stats['products_change'] ?? '0%' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Produk Aktif</p>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['active_products'] ?? 0 }}</h2>
                </div>
            </div>
        @endif

    </div>

    {{-- MY TASKS SECTION --}}
    @if(count($tasks) > 0)
    <div class="mt-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tugas Saya</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aksi yang memerlukan perhatian Anda</p>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                {{ count($tasks) }} Pending
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($tasks as $task)
            <a href="{{ $task['url'] }}" class="group p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                        {{ $task['priority'] === 'high' ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400' }}">
                        @if($task['type'] === 'pr_approval')
                            <i data-feather="check-square" class="w-5 h-5"></i>
                        @elseif($task['type'] === 'winner_approval' || $task['type'] === 'po_acceptance')
                            <i data-feather="award" class="w-5 h-5"></i>
                        @elseif($task['type'] === 'invoice_purchasing' || $task['type'] === 'invoice_finance')
                            <i data-feather="credit-card" class="w-5 h-5"></i>
                        @else
                            <i data-feather="alert-circle" class="w-5 h-5"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-gray-900 dark:text-white truncate">{{ $task['title'] }}</h4>
                            <span class="text-[10px] uppercase font-bold tracking-wider {{ $task['priority'] === 'high' ? 'text-red-600' : 'text-orange-600' }}">
                                {{ $task['priority'] }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ $task['description'] }}
                        </p>
                        <div class="mt-3 flex items-center text-primary-600 dark:text-primary-400 text-xs font-bold group-hover:gap-2 transition-all">
                            Selesaikan Sekarang <i data-feather="arrow-right" class="w-3 h-3 ml-1"></i>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif


    {{-- CHART & BREAKDOWN --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-10">

        {{-- Sales/Purchases Chart --}}
        <div class="lg:col-span-2 p-8 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $isBuyer ? 'Tren Pembelian' : 'Tren Penjualan' }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Overview 6 bulan terakhir</p>
                </div>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="space-y-6">
            <div class="p-8 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Statistik Cepat</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan aktivitas</p>
                    </div>
                </div>
                <div class="space-y-4">
                @if($isBuyer)
                    <a href="{{ route('procurement.pr.index') }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <i data-feather="file-text" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Purchase Requisitions</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Lihat semua PR</p>
                            </div>
                        </div>
                        <i data-feather="chevron-right" class="w-5 h-5 text-gray-400"></i>
                    </a>
                    <a href="{{ route('procurement.po.index') }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-secondary-100 dark:bg-secondary-900/30 rounded-lg flex items-center justify-center">
                                <i data-feather="shopping-bag" class="w-5 h-5 text-secondary-600 dark:text-secondary-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Purchase Orders</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Lihat semua PO</p>
                            </div>
                        </div>
                        <i data-feather="chevron-right" class="w-5 h-5 text-gray-400"></i>
                    </a>
                @else
                    <a href="{{ route('catalogue.index') }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <i data-feather="box" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">E-Catalogue</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Kelola produk Anda</p>
                            </div>
                        </div>
                        <i data-feather="chevron-right" class="w-5 h-5 text-gray-400"></i>
                    </a>
                    <a href="{{ route('procurement.po.index') }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-secondary-100 dark:bg-secondary-900/30 rounded-lg flex items-center justify-center">
                                <i data-feather="shopping-bag" class="w-5 h-5 text-secondary-600 dark:text-secondary-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Order Masuk</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Lihat PO dari buyer</p>
                            </div>
                        </div>
                        <i data-feather="chevron-right" class="w-5 h-5 text-gray-400"></i>
                    </a>
                @endif
                <a href="{{ route('companies.show', $company) }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i data-feather="briefcase" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Profil Perusahaan</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Lihat detail company</p>
                        </div>
                    </div>
                    <i data-feather="chevron-right" class="w-5 h-5 text-gray-400"></i>
                </a>
            </div>
        </div>

    </div>
@endsection


{{-- PASTE SCRIPT CHART.js DI SINI --}}
@push('scripts')
{{-- Chart.js is loaded via npm/Vite bundle --}}

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Common Chart Options
    Chart.defaults.font.family = "'Instrument Sans', sans-serif";
    Chart.defaults.color = '#6b7280';

    // Chart Data from Controller
    const chartLabels = @json($chartData['labels'] ?? []);
    const chartValues = @json($chartData['values'] ?? []);

    // SALES LINE CHART
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    
    // Gradient for line chart
    const gradient = ctxSales.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(236, 106, 45, 0.2)'); // Primary color low opacity
    gradient.addColorStop(1, 'rgba(236, 106, 45, 0)');

    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: '{{ $isBuyer ? "Pembelian" : "Penjualan" }} (juta)',
                data: chartValues,
                borderWidth: 3,
                borderColor: '#ec6a2d', // Primary 500
                backgroundColor: gradient,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#ec6a2d',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 13 },
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y + ' Juta';
                        }
                    }
                }
            },
            scales: {
                y: {
                    grid: {
                        color: '#f3f4f6',
                        drawBorder: false,
                    },
                    ticks: {
                        padding: 10,
                        callback: function(value) {
                            return 'Rp ' + value + 'M';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        padding: 10
                    }
                }
            }
        }
    });

});
</script>
@endpush
