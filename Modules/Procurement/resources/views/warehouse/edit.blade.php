@extends('layouts.app', [
    'title' => 'Modify Storage Point',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => url('/')],
        ['name' => 'Storage Points', 'url' => route('procurement.warehouse.index')],
        ['name' => 'Modify Unit', 'url' => null],
    ]
])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-12">
        <div class="flex items-center gap-3 mb-1">
            <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">CONFIGURATION</span>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">REF: {{ $warehouse->code }}</span>
        </div>
        <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-3">
            Modify <span class="text-emerald-600">Storage Unit</span>
        </h1>
        <p class="text-gray-500 font-medium">Update the physical location or naming convention for this node.</p>
    </div>

    <form action="{{ route('procurement.warehouse.update', $warehouse) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm p-10 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Unit Name</label>
                    <input type="text" name="name" id="name" required value="{{ $warehouse->name }}"
                           class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl text-[11px] font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                </div>
                
                <div class="space-y-3">
                    <label for="code" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Reference Code</label>
                    <input type="text" name="code" id="code" required value="{{ $warehouse->code }}"
                           class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl text-[11px] font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                </div>
            </div>

            <div class="space-y-3">
                <label for="address" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Delivery Address / Location</label>
                <textarea name="address" id="address" rows="4" 
                          class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border-0 rounded-2xl text-[11px] font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 transition-all outline-none resize-none">{{ $warehouse->address }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between gap-6">
            <a href="{{ route('procurement.warehouse.index') }}" class="h-16 px-10 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[11px] font-black text-gray-400 uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                Cancel
            </a>
            <button type="submit" class="h-16 flex-1 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all active:scale-[0.98]">
                Update Unit Artifact
            </button>
        </div>
    </form>
</div>
@endsection
