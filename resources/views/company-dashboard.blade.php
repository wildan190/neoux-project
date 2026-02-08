@extends('layouts.app', [
    'title' => 'Dashboard',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Dashboard', 'url' => '#']
    ]
])

@section('content')
    {{-- Header --}}
    {{-- Header with Role Toggle --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-10">
        <div>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Dashboard Overview</h2>
            <p class="text-brand-gray-500 dark:text-gray-400 mt-1">
                Selamat datang! Berikut ringkasan aktivitas <span class="text-primary-600 font-bold">{{ $company->name }}</span> hari ini.
            </p>
        </div>
        
        @if($canBeBuyer && $canBeVendor)
            <div class="flex p-1.5 bg-gray-100 dark:bg-gray-800 rounded-2xl shadow-inner-sm">
                <a href="{{ route('company.dashboard', ['view' => 'buyer']) }}" 
                   class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-black transition-all duration-300 {{ $currentView === 'buyer' ? 'bg-white dark:bg-gray-700 text-primary-600 shadow-md transform scale-[1.02]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
                    <i data-feather="shopping-bag" class="w-4 h-4"></i>
                    Acting as Buyer
                </a>
                <a href="{{ route('company.dashboard', ['view' => 'vendor']) }}" 
                   class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-black transition-all duration-300 {{ $currentView === 'vendor' ? 'bg-white dark:bg-gray-700 text-emerald-600 shadow-md transform scale-[1.02]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
                    <i data-feather="truck" class="w-4 h-4"></i>
                    Acting as Vendor
                </a>
            </div>
        @else
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest {{ $isBuyer ? 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-900/20 dark:border-blue-800' : 'bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-900/20 dark:border-emerald-800' }}">
                    <i data-feather="{{ $isBuyer ? 'shopping-bag' : 'truck' }}" class="w-4 h-4"></i>
                    {{ $isBuyer ? 'Buyer Account' : 'Vendor Account' }}
                </span>
            </div>
        @endif
    </div>

    {{-- Tasklist Section --}}
    @if(count($tasks) > 0)
        <div class="mb-10 animate-fade-in">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600">
                    <i data-feather="bell" class="w-5 h-5"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Needs Your Attention</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($tasks as $task)
                    <a href="{{ $task['route'] }}" class="group relative overflow-hidden bg-white dark:bg-gray-800 p-5 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between relative z-10">
                            <div class="w-12 h-12 rounded-2xl bg-{{ $task['color'] }}-50 dark:bg-{{ $task['color'] }}-900/20 flex items-center justify-center text-{{ $task['color'] }}-600 group-hover:scale-110 transition-transform">
                                <i data-feather="{{ $task['icon'] }}" class="w-6 h-6"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-black text-{{ $task['color'] }}-600">{{ $task['count'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 relative z-10">
                            <h4 class="font-bold text-gray-900 dark:text-white leading-tight group-hover:text-primary-600 transition-colors">{{ $task['title'] }}</h4>
                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                View Details <i data-feather="arrow-right" class="w-3 h-3"></i>
                            </p>
                        </div>
                        {{-- Subtle background decoration --}}
                        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity">
                            <i data-feather="{{ $task['icon'] }}" class="w-24 h-24"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

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



{{-- PASTE SCRIPT CHART.js DI SINI --}}
    {{-- Scripts moved inside content section to fix SPA navigation BUG --}}
    <script>
        function initSalesChart() {
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js not loaded yet, retrying...');
                setTimeout(initSalesChart, 100);
                return;
            }

            const chartCanvas = document.getElementById('salesChart');
            if (!chartCanvas) return;

            // Common Chart Options
            Chart.defaults.font.family = "'Instrument Sans', sans-serif";
            Chart.defaults.color = '#6b7280';

            // Chart Data from Controller
            const chartLabels = @json($chartData['labels'] ?? []);
            const chartValues = @json($chartData['values'] ?? []);

            const ctxSales = chartCanvas.getContext('2d');
            
            // Gradient for line chart
            const gradient = ctxSales.createLinearGradient(0, 0, 0, 400);
            const color = '{{ $isBuyer ? "#3b82f6" : "#10b981" }}'; // Blue for buyer, Emerald for vendor
            const rgbaColor = '{{ $isBuyer ? "rgba(59, 130, 246, 0.2)" : "rgba(16, 185, 129, 0.2)" }}';
            
            gradient.addColorStop(0, rgbaColor);
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            // Destroy existing chart if it exists to prevent memory leaks or reuse errors
            if (window.mySalesChart) {
                window.mySalesChart.destroy();
            }

            window.mySalesChart = new Chart(ctxSales, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: '{{ $isBuyer ? "Pembelian" : "Penjualan" }} (juta)',
                        data: chartValues,
                        borderWidth: 4,
                        borderColor: color,
                        backgroundColor: gradient,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: color,
                        pointBorderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 12,
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
                            grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                            ticks: {
                                padding: 10,
                                callback: function(value) { return 'Rp ' + value + 'M'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { padding: 10 }
                        }
                    }
                }
            });
        }

        // Run immediately and ensure feather icons are replaced
        initSalesChart();
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>
@endsection
