@extends('layouts.app', [
    'title' => 'My Workspaces',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Workspaces', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">MULTI-WORKSPACE</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global Entities: {{ $companies->count() }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                My <span class="text-primary-600">Company Entities</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('companies.create') }}" class="h-16 px-10 flex items-center bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
                Establish New Entity
            </a>
        </div>
    </div>

    {{-- Company Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($companies as $company)
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 p-10 shadow-sm group hover:border-primary-500 transition-all duration-500 flex flex-col h-full">
                <div class="flex items-start justify-between mb-10">
                    <div class="w-20 h-20 rounded-[1.75rem] bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-3 relative overflow-hidden shadow-inner group-hover:scale-110 transition-transform duration-700">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" class="w-full h-full object-contain">
                        @else
                            <i data-feather="briefcase" class="w-8 h-8 text-gray-300"></i>
                        @endif
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-600',
                            'active' => 'bg-emerald-100 text-emerald-600',
                            'declined' => 'bg-red-100 text-red-600',
                        ];
                    @endphp
                    <span class="px-3 py-1.5 {{ $statusColors[$company->status] ?? 'bg-gray-100 text-gray-500' }} text-[9px] font-black rounded-lg uppercase tracking-widest">
                        {{ $company->status }}
                    </span>
                </div>

                <div class="mb-10 flex-1">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 group-hover:text-primary-600 transition-colors">{{ $company->name }}</h3>
                    <p class="text-[10px] font-black text-primary-600 uppercase tracking-widest mb-6">{{ $company->category }} SECTOR</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed line-clamp-2">
                        {{ $company->description ?: 'NO DETAILED DESCRIPTION PROVIDED FOR THIS ENTITY.' }}
                    </p>
                </div>

                <div class="pt-8 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between mt-auto">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 leading-none">Registration</span>
                        <span class="text-xs font-black text-gray-900 dark:text-white tabular-nums">{{ $company->registration_number }}</span>
                    </div>
                    <a href="{{ route('companies.show', $company) }}" class="h-12 w-12 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 group-hover:bg-primary-600 group-hover:text-white transition-all shadow-sm">
                        <i data-feather="arrow-right" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 bg-gray-50/50 dark:bg-gray-900/30 rounded-[3rem] border border-dashed border-gray-200 dark:border-gray-800 text-center">
                <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-gray-200 shadow-sm border border-gray-100 dark:border-gray-800">
                    <i data-feather="plus" class="w-10 h-10"></i>
                </div>
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Zero workspaces established</h3>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-relaxed">Initiate your first company node to begin trading operations.</p>
                <a href="{{ route('companies.create') }}" class="mt-8 inline-flex h-14 px-10 items-center bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all">
                    Register Entity
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
