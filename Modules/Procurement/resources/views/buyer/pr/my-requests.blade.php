@extends('layouts.app', [
    'title' => 'My Activity',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'My Requests', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">Personal Workspace</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Activity Log</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                My <span class="text-primary-600">Requests</span>
            </h1>
        </div>
        
        <a href="{{ route('procurement.pr.create') }}" 
           class="h-16 px-10 flex items-center bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Initialize New PR
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 p-4 border border-emerald-100 dark:border-emerald-900/30 flex items-center gap-3 text-emerald-700 dark:text-emerald-400">
            <i data-feather="check-circle" class="h-5 w-5"></i>
            <p class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</p>
        </div>
    @endif

    {{-- 3-Column Layout for Consistency --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- LEFT: Personal Stats --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-[2rem] flex items-center justify-center text-primary-600 mx-auto mb-4 shadow-inner">
                    <i data-feather="user" class="w-10 h-10"></i>
                </div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">{{ Auth::user()->name }}</h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Authorized Requester</p>
                
                <div class="grid grid-cols-1 gap-4 mt-8 pt-8 border-t border-gray-50 dark:border-gray-800">
                    <div>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $requisitions->total() }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total Submissions</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CENTER/RIGHT: Table --}}
        <div class="lg:col-span-9">
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-800">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Title & Reference</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Company Context</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Approval Status</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Items</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($requisitions as $pr)
                            <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors uppercase">{{ $pr->title }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $pr->pr_number }} • {{ $pr->created_at->format('d M Y') }}</p>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center border border-gray-100 dark:border-gray-700 overflow-hidden shrink-0">
                                            @if($pr->company && $pr->company->logo_url)
                                                <img class="w-full h-full object-contain p-1" src="{{ $pr->company->logo_url }}" alt="">
                                            @else
                                                <span class="text-[10px] font-black text-gray-400">{{ substr($pr->company->name ?? 'NA', 0, 2) }}</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] font-bold text-gray-600 dark:text-gray-300 truncate max-w-[120px]">{{ $pr->company->name ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-gray-100 text-gray-500',
                                            'pending' => 'bg-yellow-50 text-yellow-600',
                                            'approved' => 'bg-emerald-50 text-emerald-600',
                                            'rejected' => 'bg-red-50 text-red-600',
                                        ];
                                        $class = $statusClasses[$pr->approval_status] ?? 'bg-gray-100 text-gray-500';
                                    @endphp
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $class }} border border-current opacity-80">
                                        {{ str_replace('_', ' ', $pr->approval_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-gray-900 dark:text-white">{{ $pr->items->count() }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">SKUs</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('procurement.pr.show', $pr) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-gray-400 hover:text-primary-600 transition-all uppercase tracking-widest">
                                        Open Record
                                        <i data-feather="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-3xl flex items-center justify-center text-gray-200 mx-auto mb-4 border border-gray-100 dark:border-gray-700">
                                        <i data-feather="inbox" class="w-8 h-8"></i>
                                    </div>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">You have not submitted any requisitions yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($requisitions->hasPages())
                    <div class="p-8 border-t border-gray-50 dark:border-gray-800">
                        {{ $requisitions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
@endsection
