@extends('layouts.app', [
    'title' => 'Entity Verification',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Company Entities', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">ENTITY AUDIT</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Verification Queue</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                Company <span class="text-primary-600 font-medium">Verification</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-6 bg-white dark:bg-gray-800 p-4 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800">
            <a href="{{ route('admin.companies.index', ['status' => 'pending']) }}" class="flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-xl bg-yellow-100 dark:bg-yellow-900/20 flex items-center justify-center text-yellow-600 group-hover:scale-110 transition-transform">
                    <i data-feather="clock" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Pending</p>
                    <p class="text-sm font-black text-gray-900 dark:text-white leading-none">{{ $counts['pending'] }}</p>
                </div>
            </a>
            <div class="w-px h-8 bg-gray-100 dark:bg-gray-700"></div>
            <a href="{{ route('admin.companies.index', ['status' => 'active']) }}" class="flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <i data-feather="check-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Approved</p>
                    <p class="text-sm font-black text-gray-900 dark:text-white leading-none">{{ $counts['active'] }}</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 p-1.5 bg-gray-100 dark:bg-gray-900 rounded-2xl w-fit">
        @foreach(['all' => 'All Entities', 'pending' => 'Pending Review', 'active' => 'Approved', 'declined' => 'Declined'] as $key => $label)
            <a href="{{ route('admin.companies.index', ['status' => $key]) }}" 
                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $status === $key ? 'bg-white dark:bg-gray-800 text-primary-600 shadow-sm' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                {{ $label }}
                <span class="ml-2 px-1.5 py-0.5 bg-gray-200 dark:bg-gray-700 rounded-md text-[8px] text-gray-500">{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Entity Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                        <th class="px-10 py-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Enterprise</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Sector / Category</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Review Status</th>
                        <th class="px-10 py-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($companies as $company)
                        <tr class="group hover:bg-gray-50/30 dark:hover:bg-gray-900/30 transition-colors text-xs">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-6">
                                    <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center relative overflow-hidden shadow-inner group-hover:scale-105 transition-transform duration-500">
                                        @if($company->logo)
                                            <img src="{{ asset('storage/' . $company->logo) }}" class="w-full h-full object-cover">
                                        @else
                                            <i data-feather="briefcase" class="w-5 h-5 text-gray-300"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2 group-hover:text-primary-600 transition-colors">
                                            {{ $company->name }}
                                        </p>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none">REG #{{ $company->registration_number }}</span>
                                            <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                            <span class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest leading-none">{{ $company->created_at->format('M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 text-[9px] font-bold rounded-lg uppercase tracking-widest">
                                    {{ $company->category }}
                                </span>
                            </td>
                            <td class="px-10 py-6">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'declined' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                @endphp
                                <span class="px-3 py-1.5 {{ $statusColors[$company->status] ?? 'bg-gray-100 text-gray-500' }} text-[9px] font-bold rounded-lg uppercase tracking-widest inline-flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>
                                    {{ $company->status }}
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <a href="{{ route('admin.companies.show', $company) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-900 text-white hover:bg-primary-600 transition-all shadow-lg shadow-gray-900/10 hover:shadow-primary-600/20">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 py-24 text-center">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center mx-auto mb-6 text-gray-200 shadow-inner">
                                    <i data-feather="layers" class="w-8 h-8"></i>
                                </div>
                                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Zero entities awaiting review</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
        @if($companies->hasPages())
            <div class="px-10 py-8 border-t border-gray-50 dark:border-gray-800">
                {{ $companies->links() }}
            </div>
        @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
