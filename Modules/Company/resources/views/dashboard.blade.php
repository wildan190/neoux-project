@extends('layouts.app', [
    'title' => $company->name . ' Dashboard',
    'breadcrumbs' => [
        ['name' => 'Workspace', 'url' => url('/')],
        ['name' => $company->name, 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="flex items-center gap-8">
            <div class="w-24 h-24 rounded-[2rem] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 flex items-center justify-center p-4 shadow-xl shadow-gray-200/50">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" class="w-full h-full object-contain">
                @else
                    <i data-feather="briefcase" class="w-10 h-10 text-gray-300"></i>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ strtoupper($company->category) }} WORKSPACE</span>
                    <span class="text-xs font-bold text-emerald-500 uppercase tracking-widest">Global verified</span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2">{{ $company->name }}</h1>
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest leading-none">Registration: {{ $company->registration_number }}</p>
            </div>
        </div>

        <div class="hidden xl:flex items-center gap-12 bg-white dark:bg-gray-800 px-10 py-6 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm">
            @if($isBuyer)
                <div class="text-center">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 leading-none">Total Procurement</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums leading-none">Rp{{ number_format($stats['spend_analyst']['total_spend'], 0, ',', '.') }}</p>
                </div>
                <div class="w-px h-8 bg-gray-100 dark:bg-gray-700"></div>
                <div class="text-center">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 leading-none">Cost Savings</p>
                    <p class="text-xl font-black text-emerald-500 tabular-nums leading-none">Rp{{ number_format($stats['cost_management']['cost_savings'], 0, ',', '.') }}</p>
                </div>
            @else
                <div class="text-center">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 leading-none">Total Revenue</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white tabular-nums leading-none">Rp{{ number_format($stats['total_sales'], 0, ',', '.') }}</p>
                </div>
                <div class="w-px h-8 bg-gray-100 dark:bg-gray-700"></div>
                <div class="text-center">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 leading-none">Win rate</p>
                    <p class="text-xl font-black text-emerald-500 tabular-nums leading-none">{{ $stats['orders_change'] }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Main Activity Terminal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            {{-- Performance Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Financial Velocity</h3>
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-2 text-[9px] font-black text-primary-600 uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-primary-600"></span> 6 MOS Trend
                        </span>
                    </div>
                </div>
                <div class="h-[300px] w-full relative">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            {{-- Pending Critical Tasks --}}
            <div class="space-y-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Action required queue</h3>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[9px] font-black rounded-lg uppercase tracking-widest">{{ count($tasks) }} PENDING</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($tasks as $task)
                        <a href="{{ $task['url'] }}" class="flex flex-col p-8 bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm group hover:border-primary-500 transition-all duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 group-hover:text-primary-600 transition-all shadow-inner">
                                    @if($task['type'] === 'pr_approval') <i data-feather="file-text" class="w-6 h-6"></i>
                                    @elseif($task['type'] === 'invoice_finance') <i data-feather="dollar-sign" class="w-6 h-6"></i>
                                    @elseif($task['type'] === 'po_acceptance') <i data-feather="package" class="w-6 h-6"></i>
                                    @else <i data-feather="activity" class="w-6 h-6"></i> @endif
                                </div>
                                @if($task['priority'] === 'high')
                                    <span class="px-3 py-1 bg-red-100 text-red-600 text-[8px] font-black rounded-md uppercase tracking-widest">Urgent</span>
                                @endif
                            </div>
                            <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 leading-tight group-hover:text-primary-600 transition-colors">{{ $task['title'] }}</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed mb-6">{{ $task['description'] }}</p>
                            <div class="mt-auto pt-6 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-[9px] font-black text-primary-600 uppercase tracking-widest">Execute Task</span>
                                <i data-feather="chevron-right" class="w-4 h-4 text-gray-300 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-16 bg-gray-50/50 dark:bg-gray-900/30 rounded-[3rem] border border-dashed border-gray-100 dark:border-gray-800 text-center">
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">All nodes operational - zero pending tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Quick Stats & Activity --}}
        <div class="space-y-12">
            {{-- Unified Stat Group --}}
            <div class="bg-gray-900 rounded-[3.5rem] p-10 text-white shadow-2xl relative overflow-hidden flex flex-col h-full">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary-600/20 rounded-full blur-[80px] pointer-events-none"></div>
                
                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-12 relative z-10">Strategic Metrics</h3>
                
                <div class="space-y-10 relative z-10 flex-1">
                    @if($isBuyer)
                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3 leading-none">Compliance rating</p>
                            <div class="flex items-center gap-4">
                                <span class="text-4xl font-black text-white tabular-nums leading-none">{{ 100 - round(($stats['spend_analyst']['maverick_spend'] / ($stats['spend_analyst']['total_spend'] ?: 1)) * 100) }}%</span>
                                <div class="w-px h-8 bg-white/10"></div>
                                <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none">Network benchmark: 88%</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3 leading-none">Operational efficiency</p>
                            <div class="flex items-center gap-4">
                                <span class="text-4xl font-black text-white tabular-nums leading-none">{{ $stats['operational_efficiency']['avg_cycle_time'] }}D</span>
                                <div class="w-px h-8 bg-white/10"></div>
                                <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none">Cycle Time (Avg)</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3 leading-none">Supply reliability</p>
                            <div class="flex items-center gap-4">
                                <span class="text-4xl font-black text-emerald-500 tabular-nums leading-none">{{ $stats['supplier_performance']['fill_rate'] }}%</span>
                                <div class="w-px h-8 bg-white/10"></div>
                                <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none">Global Fill Rate</span>
                            </div>
                        </div>
                    @else
                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3 leading-none">Market Penetration</p>
                            <div class="flex items-center gap-4">
                                <span class="text-4xl font-black text-white tabular-nums leading-none">{{ $stats['active_products'] }}</span>
                                <div class="w-px h-8 bg-white/10"></div>
                                <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest leading-none">{{ $stats['products_change'] }} Growth</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3 leading-none">Settlement Ratio</p>
                            <div class="flex items-center gap-4">
                                <span class="text-4xl font-black text-white tabular-nums leading-none">{{ round(($stats['invoice_amount'] / ($stats['total_sales'] ?: 1)) * 100) }}%</span>
                                <div class="w-px h-8 bg-white/10"></div>
                                <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none">Invoiced vs Sales</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3 leading-none">Negotiation Pipeline</p>
                            <div class="flex items-center gap-4">
                                <span class="text-4xl font-black text-primary-500 tabular-nums leading-none">{{ $stats['active_orders'] }}</span>
                                <div class="w-px h-8 bg-white/10"></div>
                                <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none">Pending Tenders</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-12 pt-12 border-t border-white/5 relative z-10">
                    <div class="p-6 bg-white/5 rounded-3xl backdrop-blur-md">
                        <div class="flex items-center gap-4">
                            <i data-feather="cpu" class="w-6 h-6 text-primary-500"></i>
                            <div>
                                <p class="text-[9px] font-black text-white uppercase tracking-widest mb-1 leading-none">AI Insight Generator</p>
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-tight">Optimization protocols ready for execution.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Team Snapshot --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active nodes</h3>
                    <a href="{{ route('team.index') }}" class="text-[9px] font-black text-primary-600 uppercase tracking-widest">Manage</a>
                </div>
                <div class="flex -space-x-3 overflow-hidden">
                    @foreach($company->members->take(5) as $member)
                        <div class="inline-block h-10 w-10 rounded-full ring-4 ring-white dark:ring-gray-800 bg-gray-100 dark:bg-gray-900 border border-transparent overflow-hidden shadow-sm" title="{{ $member->name }}">
                            <span class="flex items-center justify-center h-full w-full text-[10px] font-black text-gray-400 uppercase">{{ substr($member->name ?: $member->email, 0, 1) }}</span>
                        </div>
                    @endforeach
                    @if($company->members->count() > 5)
                        <div class="inline-block h-10 w-10 rounded-full ring-4 ring-white dark:ring-gray-800 bg-gray-900 border border-transparent overflow-hidden shadow-sm">
                            <span class="flex items-center justify-center h-full w-full text-[9px] font-black text-white uppercase">+{{ $company->members->count() - 5 }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();

        const ctx = document.getElementById('performanceChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Volume (M)',
                    data: @json($chartData['values']),
                    borderColor: '#2563eb',
                    borderWidth: 4,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        titleFont: { size: 10, weight: '900', family: 'Inter' },
                        bodyFont: { size: 10, weight: 'bold', family: 'Inter' }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9, weight: 'bold', family: 'Inter' }, color: '#9ca3af' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: 'rgba(156, 163, 175, 0.1)' },
                        ticks: { font: { size: 9, weight: 'bold', family: 'Inter' }, color: '#9ca3af' }
                    }
                }
            }
        });
    });
</script>
@endpush
