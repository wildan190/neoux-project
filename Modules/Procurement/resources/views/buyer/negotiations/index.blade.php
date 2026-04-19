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
                <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">PROCUREMENT PROCESS</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $offers->total() }} Active Negotiations</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Active Negotiations</h1>
        </div>
    </div>

    @if($offers->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-feather="message-square" class="w-10 h-10 text-gray-200"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">No Active Negotiations</h3>
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest px-12 leading-relaxed">
                You currently have no offers in the negotiating stage.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 mb-8">
            @foreach($offers as $offer)
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 p-8 shadow-sm transition-all hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-none relative group ring-2 ring-primary-500">
                    
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $offer->purchaseRequisition->title }}</h3>
                                <span class="px-2 py-0.5 bg-primary-100 text-primary-700 text-[8px] font-black uppercase tracking-widest rounded-md">NEGOTIATING</span>
                            </div>
                            
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 leading-relaxed">
                                VENDOR: {{ $offer->company->name }} <br>
                                LAST UPDATED: {{ $offer->updated_at->format('d M Y, H:i') }}
                            </p>

                            <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-800">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Latest Message</p>
                                <p class="text-xs text-gray-700 dark:text-gray-300">{{ $offer->negotiation_message ?? 'Check offer details for terms.' }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col lg:items-end gap-3 rounded-2xl min-w-[200px]">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Current Bid Amount</p>
                                <p class="text-3xl font-black text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}</p>
                            </div>
                            
                            <div class="flex items-center gap-2 mt-2">
                                <a href="{{ route('procurement.offers.show', $offer) }}"
                                   class="w-full text-center px-5 py-2.5 bg-gray-900 text-white dark:bg-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gray-900/10 hover:scale-105 transition-all">
                                    Review Terms
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
