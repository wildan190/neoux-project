@extends('layouts.app', [
    'title' => 'Active Negotiations',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Negotiations', 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $tab === 'active' ? 'Active' : 'Historical' }} Pipelines</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $offers->total() }} Projects</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                {{ $tab === 'active' ? 'Sales Negotiations' : 'Project History' }}
            </h1>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-2 mb-8 bg-gray-50 dark:bg-gray-900/50 p-1.5 rounded-[1.5rem] w-fit border border-gray-100 dark:border-gray-800">
        <a href="{{ route('procurement.offers.negotiations', ['tab' => 'active']) }}" 
            class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'active' ? 'bg-white dark:bg-gray-800 text-primary-600 shadow-sm border border-gray-100 dark:border-gray-700' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            Active ({{ $active_count }})
        </a>
        <a href="{{ route('procurement.offers.negotiations', ['tab' => 'history']) }}" 
            class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'history' ? 'bg-white dark:bg-gray-800 text-primary-600 shadow-sm border border-gray-100 dark:border-gray-700' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }}">
            History ({{ $history_count }})
        </a>
    </div>

    @if($offers->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-feather="{{ $tab === 'active' ? 'trending-up' : 'archive' }}" class="w-10 h-10 text-gray-200"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                {{ $tab === 'active' ? 'No Active Negotiations' : 'No History Found' }}
            </h3>
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest px-12 leading-relaxed">
                {{ $tab === 'active' ? 'You currently have no biddings in the negotiating stage.' : 'Your historical biddings will appear here once finalized.' }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 mb-8">
            @foreach($offers as $offer)
                @php
                    $statusColors = [
                        'negotiating' => 'bg-primary-100 text-primary-700 ring-primary-200',
                        'accepted' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                        'rejected' => 'bg-red-100 text-red-700 ring-red-200',
                        'winning' => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
                    ];
                    $currentColor = $statusColors[$offer->status] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 p-8 shadow-sm transition-all hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-none relative group {{ $offer->status === 'negotiating' ? 'ring-2 ring-primary-500' : '' }}">
                    
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $offer->purchaseRequisition->title }}</h3>
                                <span class="px-2 py-0.5 {{ $currentColor }} text-[8px] font-black uppercase tracking-widest rounded-md ring-1 italic">{{ $offer->status === 'negotiating' ? 'Action Required' : str_replace('_', ' ', $offer->status) }}</span>
                            </div>
                            
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 leading-relaxed">
                                CLIENT: {{ $offer->purchaseRequisition->company->name }} <br>
                                REVISED ON: {{ $offer->updated_at->format('d M Y, H:i') }}
                            </p>

                            <div class="bg-primary-50 dark:bg-primary-900/20 px-4 py-3 rounded-xl border border-primary-100 dark:border-primary-800/30">
                                <p class="text-[8px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-widest mb-1">Client's Negotiation Note</p>
                                <p class="text-xs text-primary-900 dark:text-primary-100 italic">"{{ $offer->negotiation_message ?? 'Check offer details for requested terms.' }}"</p>
                            </div>
                        </div>

                        <div class="flex flex-col lg:items-end gap-3 rounded-2xl min-w-[200px]">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Bid Value</p>
                                <p class="text-3xl font-black text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}</p>
                            </div>
                            
                            <div class="flex items-center gap-2 mt-2">
                                <a href="{{ route('procurement.offers.show', $offer) }}"
                                   class="w-full text-center px-5 py-2.5 bg-gray-900 text-white dark:bg-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gray-900/10 hover:scale-105 transition-all">
                                    {{ $offer->status === 'negotiating' ? 'Respond Now' : 'View Outcome' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($offers->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $offers->links() }}
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endpush
