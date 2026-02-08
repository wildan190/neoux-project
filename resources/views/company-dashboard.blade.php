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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        @if($isBuyer)
            {{-- Buyer Widgets --}}
            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="shopping-cart" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                        <i data-feather="trending-up" class="w-3 h-3"></i> {{ $stats['purchases_change'] ?? '+0%' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pembelian</p>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format(($stats['total_purchases'] ?? 0) / 1000000, 1) }}M</h2>
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
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">PO Aktif</p>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['active_orders'] ?? 0 }}</h2>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="users" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                        <i data-feather="trending-up" class="w-3 h-3"></i> {{ $stats['vendors_change'] ?? '+0%' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Vendor</p>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_vendors'] ?? 0 }}</h2>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-feather="file-text" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <span class="text-xs font-bold text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                        <i data-feather="clock" class="w-3 h-3"></i> Pending
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">PR Pending</p>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pending_pr'] ?? 0 }}</h2>
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


    {{-- CHART --}}
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
                <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i data-feather="more-horizontal" class="w-5 h-5 text-gray-400"></i>
                </button>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Quick Stats --}}
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
