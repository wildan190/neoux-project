@extends('layouts.app', [
    'title' => 'Dashboard',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Dashboard', 'url' => '#']
    ]
])

@section('content')

{{-- WIDGET TOP --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <div class="p-6 bg-white dark:bg-gray-800 shadow rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Penjualan</p>
                <h2 class="text-2xl font-bold mt-1">Rp 125.400.000</h2>
            </div>
            <i data-feather="shopping-cart" class="w-10 h-10 text-indigo-500"></i>
        </div>
    </div>

    <div class="p-6 bg-white dark:bg-gray-800 shadow rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pembelian</p>
                <h2 class="text-2xl font-bold mt-1">Rp 82.900.000</h2>
            </div>
            <i data-feather="package" class="w-10 h-10 text-pink-500"></i>
        </div>
    </div>

    <div class="p-6 bg-white dark:bg-gray-800 shadow rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                <h2 class="text-2xl font-bold mt-1">1.254</h2>
            </div>
            <i data-feather="users" class="w-10 h-10 text-green-500"></i>
        </div>
    </div>

    <div class="p-6 bg-white dark:bg-gray-800 shadow rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Produk Aktif</p>
                <h2 class="text-2xl font-bold mt-1">842</h2>
            </div>
            <i data-feather="box" class="w-10 h-10 text-yellow-500"></i>
        </div>
    </div>

</div>


{{-- CHART --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Sales Chart --}}
    <div class="lg:col-span-2 p-6 bg-white dark:bg-gray-800 shadow rounded-xl">
        <h3 class="font-bold text-lg mb-4">Grafik Penjualan</h3>
        <canvas id="salesChart" height="120"></canvas>
    </div>

    {{-- Stock Chart --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow rounded-xl">
        <h3 class="font-bold text-lg mb-4">Stok Gudang</h3>
        <canvas id="stockChart" height="120"></canvas>
    </div>

</div>

@endsection


{{-- PASTE SCRIPT CHART.js DI SINI --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // SALES LINE CHART
    new Chart(document.getElementById('salesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [{
                label: 'Penjualan (juta)',
                data: [20, 35, 40, 38, 50, 60],
                borderWidth: 3,
                borderColor: '#6366f1',
                pointBackgroundColor: '#6366f1',
                tension: 0.4
            }]
        }
    });

    // STOCK DOUGHNUT CHART
    new Chart(document.getElementById('stockChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Bahan Baku', 'Barang Jadi', 'Dalam Proses'],
            datasets: [{
                data: [40, 35, 25],
                backgroundColor: ['#34d399', '#60a5fa', '#f87171']
            }]
        }
    });

});
</script>
