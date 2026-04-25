@extends('layouts.app', [
    'title' => 'Purchase Requisitions',
    'breadcrumbs' => [
        ['name' => 'Shopping', 'url' => route('procurement.marketplace.index')],
        ['name' => 'Company PR', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    {{-- Header Section --}}
    <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 p-10 text-primary-500/5 pointer-events-none">
            <i data-feather="file-text" style="width:180px;height:180px;"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">Internal Logistics</span>
                    <div class="h-px w-8 bg-gray-200 dark:bg-gray-700"></div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Company Registry</span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2">
                    Purchase <span class="text-primary-600">Requisitions</span>
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Manage and track your company's internal procurement requests and approval status.</p>
            </div>
            
            <a href="{{ route('procurement.pr.create') }}" 
               class="h-16 px-10 flex items-center bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-900/20 hover:bg-primary-600 transition-all active:scale-[0.98]">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                New Request
            </a>
        </div>
    </div>

    {{-- Main 3-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- LEFT: Filters --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-2 shadow-sm">
                <div class="space-y-1">
                    <a href="{{ route('procurement.pr.index', ['filter' => 'open']) }}" 
                       class="flex items-center justify-between px-5 py-4 rounded-2xl transition-all group {{ $filter === 'open' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <i data-feather="clock" class="w-4 h-4 {{ $filter === 'open' ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                            <span class="text-[11px] font-black uppercase tracking-widest">Active ({{ $openCount }})</span>
                        </div>
                    </a>
                    <a href="{{ route('procurement.pr.index', ['filter' => 'closed']) }}" 
                       class="flex items-center justify-between px-5 py-4 rounded-2xl transition-all group {{ $filter === 'closed' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <i data-feather="check-circle" class="w-4 h-4 {{ $filter === 'closed' ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                            <span class="text-[11px] font-black uppercase tracking-widest">Closed ({{ $closedCount }})</span>
                        </div>
                    </a>
                    <a href="{{ route('procurement.pr.index', ['filter' => 'all']) }}" 
                       class="flex items-center justify-between px-5 py-4 rounded-2xl transition-all group {{ $filter === 'all' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <i data-feather="list" class="w-4 h-4 {{ $filter === 'all' ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}"></i>
                            <span class="text-[11px] font-black uppercase tracking-widest">All Activity</span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="bg-primary-50 dark:bg-primary-900/10 rounded-3xl border border-primary-100 dark:border-primary-900/30 p-6">
                <p class="text-[10px] font-black text-primary-700 dark:text-primary-400 uppercase tracking-widest mb-3">Direct Purchase</p>
                <p class="text-[10px] font-medium text-primary-700/70 dark:text-primary-400/70 leading-relaxed">
                    Requisitions for marketplace items will be automatically routed to respective vendors upon approval.
                </p>
            </div>
        </div>

        {{-- CENTER: Table --}}
        <div class="lg:col-span-9">
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-800">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Request Details</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Requester</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
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
                                        <div class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[10px] font-black text-gray-500 overflow-hidden shadow-inner">
                                            @if($pr->user->userDetail && $pr->user->userDetail->profile_photo_url)
                                                <img class="w-full h-full object-cover" src="{{ $pr->user->userDetail->profile_photo_url }}" alt="">
                                            @else
                                                {{ substr($pr->user->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <p class="text-[11px] font-bold text-gray-600 dark:text-gray-300">{{ $pr->user->name }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-gray-100 text-gray-500',
                                            'pending' => 'bg-yellow-50 text-yellow-600',
                                            'approved' => 'bg-emerald-50 text-emerald-600',
                                            'rejected' => 'bg-red-50 text-red-600',
                                            'ordered' => 'bg-blue-50 text-blue-600',
                                            'awarded' => 'bg-purple-50 text-purple-600',
                                        ];
                                        $currentStatus = in_array($pr->status, ['awarded', 'ordered']) ? $pr->status : $pr->approval_status;
                                        $class = $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-500';
                                    @endphp
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $class }} border border-current opacity-80">
                                        {{ $currentStatus }}
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
                                        View Details
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
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">No requisitions found in this filter.</p>
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
