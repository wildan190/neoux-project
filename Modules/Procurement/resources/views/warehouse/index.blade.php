@extends('layouts.app', [
    'title' => 'Storage Points (Warehouses)',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => url('/')],
        ['name' => 'Storage Points', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">LOGISTICS NODES</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global Storage Registry</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                Storage <span class="text-emerald-600">Points</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('procurement.warehouse.create') }}" class="h-16 px-10 flex items-center bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-900/20 hover:bg-black transition-all active:scale-[0.98]">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Add New Point
            </a>
        </div>
    </div>

    {{-- Warehouse Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($warehouses as $wh)
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl hover:shadow-gray-200/20 transition-all p-8 flex flex-col justify-between group">
                <div>
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner group-hover:bg-emerald-50 transition-colors">
                            <i data-feather="map-pin" class="w-6 h-6"></i>
                        </div>
                        <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $wh->code }}</span>
                    </div>
                    
                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 group-hover:text-emerald-600 transition-colors">{{ $wh->name }}</h3>
                    <p class="text-xs font-medium text-gray-500 line-clamp-2 italic mb-6">
                        {{ $wh->address ?? 'No address registered for this unit.' }}
                    </p>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-50 dark:border-gray-800/50">
                    <a href="{{ route('procurement.warehouse.edit', $wh) }}" class="flex-1 h-12 flex items-center justify-center bg-gray-50 dark:bg-gray-900 text-[10px] font-black text-gray-400 uppercase tracking-widest rounded-xl hover:bg-primary-50 hover:text-primary-600 transition-all">
                        Edit Point
                    </a>
                    <form action="{{ route('procurement.warehouse.destroy', $wh) }}" method="POST" class="shrink-0" onsubmit="return confirm('Archive this storage point?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-12 h-12 flex items-center justify-center bg-red-50/50 dark:bg-red-900/10 text-red-400 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-gray-50 dark:bg-gray-900/10 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-gray-800 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-3xl flex items-center justify-center text-gray-200 mb-6 shadow-sm">
                    <i data-feather="map" class="w-10 h-10"></i>
                </div>
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-2">No Storage Points Detected</h3>
                <p class="text-xs text-gray-400 max-w-xs">You need at least one storage point to perform Goods Receipts and track inventory.</p>
                <a href="{{ route('procurement.warehouse.create') }}" class="mt-8 text-[11px] font-black text-emerald-600 uppercase tracking-widest border-b-2 border-emerald-600 pb-1">Initialize First Point</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
