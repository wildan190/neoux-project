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
            <p class="text-gray-500 dark:text-gray-400 mt-1">Welcome back! Here's what's happening with your business today.</p>
        </div>
        <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-1.5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <button class="px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 dark:bg-primary-900/30 rounded-lg transition-colors">Last 30 Days</button>
            <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">This Year</button>
        </div>
    </div>

    {{-- WIDGET TOP --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-feather="shopping-cart" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                    <i data-feather="trending-up" class="w-3 h-3"></i> +12.5%
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales</p>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">Rp 125.4M</h2>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-secondary-50 dark:bg-secondary-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-feather="package" class="w-6 h-6 text-secondary-600 dark:text-secondary-400"></i>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                    <i data-feather="trending-up" class="w-3 h-3"></i> +8.2%
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Purchases</p>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">Rp 82.9M</h2>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-feather="users" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                    <i data-feather="trending-up" class="w-3 h-3"></i> +5.3%
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Customers</p>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">1,254</h2>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-feather="box" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <span class="text-xs font-bold text-red-600 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-lg flex items-center gap-1">
                    <i data-feather="trending-down" class="w-3 h-3"></i> -2.1%
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Products</p>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">842</h2>
            </div>
        </div>

    </div>


    {{-- CHART --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-10">

        {{-- Sales Chart --}}
        <div class="lg:col-span-2 p-8 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Sales Analytics</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monthly performance overview</p>
                </div>
                <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i data-feather="more-horizontal" class="w-5 h-5 text-gray-400"></i>
                </button>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Stock Chart --}}
        <div class="p-8 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Inventory Status</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Current stock distribution</p>
                </div>
            </div>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="stockChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-primary-500"></span>
                        <span class="text-gray-600 dark:text-gray-300">Raw Materials</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">40%</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-secondary-400"></span>
                        <span class="text-gray-600 dark:text-gray-300">Finished Goods</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">35%</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                        <span class="text-gray-600 dark:text-gray-300">In Progress</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">25%</span>
                </div>
            </div>
        </div>

    </div>
@endsection


{{-- PASTE SCRIPT CHART.js DI SINI --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Common Chart Options
    Chart.defaults.font.family = "'Instrument Sans', sans-serif";
    Chart.defaults.color = '#6b7280';

    // SALES LINE CHART
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    
    // Gradient for line chart
    const gradient = ctxSales.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(236, 106, 45, 0.2)'); // Primary color low opacity
    gradient.addColorStop(1, 'rgba(236, 106, 45, 0)');

    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [{
                label: 'Penjualan (juta)',
                data: [20, 35, 40, 38, 50, 60],
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
                    displayColors: false
                }
            },
            scales: {
                y: {
                    grid: {
                        color: '#f3f4f6',
                        drawBorder: false,
                    },
                    ticks: {
                        padding: 10
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

    // STOCK DOUGHNUT CHART
    new Chart(document.getElementById('stockChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Bahan Baku', 'Barang Jadi', 'Dalam Proses'],
            datasets: [{
                data: [40, 35, 25],
                backgroundColor: ['#ec6a2d', '#f5c343', '#e5e7eb'], // Primary, Secondary, Gray
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

});
</script>
