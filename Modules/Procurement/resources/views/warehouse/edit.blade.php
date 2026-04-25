@extends('layouts.app', [
    'title' => 'Modify Storage Point',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => url('/')],
        ['name' => 'Storage Points', 'url' => route('procurement.warehouse.index')],
        ['name' => 'Modify Unit', 'url' => null],
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Page Header --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 p-10 text-emerald-500/5 pointer-events-none">
            <i data-feather="edit" style="width:180px;height:180px;"></i>
        </div>
        <div class="relative z-10 flex items-center gap-3 mb-4">
            <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">Configuration</span>
            <div class="h-px w-8 bg-gray-200 dark:bg-gray-700"></div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Warehouse Module</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2 relative z-10">
            Modify <span class="text-emerald-600">Storage Unit</span>
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium relative z-10">
            Update the physical location or naming convention for the unit <span class="font-bold text-gray-900 dark:text-white">{{ $warehouse->code }}</span>.
        </p>
    </div>

    {{-- Main 3-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- LEFT: Guide Panel --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600">
                        <i data-feather="info" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Field Guide</p>
                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">What to fill in</p>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="flex gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center shrink-0 mt-0.5">
                            <i data-feather="archive" class="w-3.5 h-3.5 text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">Unit Name</p>
                            <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 leading-relaxed">The official name of the storage facility.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center shrink-0 mt-0.5">
                            <i data-feather="hash" class="w-3.5 h-3.5 text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">Reference Code</p>
                            <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 leading-relaxed">A unique short code used in tracking.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CENTER: Main Form --}}
        <div class="lg:col-span-6">
            <form action="{{ route('procurement.warehouse.update', $warehouse) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-2 pb-4 border-b border-gray-50 dark:border-gray-800">
                        <div class="w-9 h-9 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-feather="edit-3" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Update Details</p>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Modify storage node properties</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Unit Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $warehouse->name) }}"
                               class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                        @error('name') <p class="text-[10px] text-red-500 font-bold uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="code" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Reference Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" required value="{{ old('code', $warehouse->code) }}"
                               class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                        @error('code') <p class="text-[10px] text-red-500 font-bold uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="address" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Delivery Address / Location</label>
                        <textarea name="address" id="address" rows="4"
                                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none resize-none">{{ old('address', $warehouse->address) }}</textarea>
                        @error('address') <p class="text-[10px] text-red-500 font-bold uppercase">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('procurement.warehouse.index') }}"
                       class="h-12 px-6 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-[10px] font-black text-gray-500 uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                            class="h-12 flex-1 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 transition-all active:scale-[0.98]">
                        Update Storage Unit
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT: Status / Info Panel --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-gray-900 dark:bg-black rounded-2xl border border-gray-800 p-6 text-white relative overflow-hidden">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/5 relative z-10">
                    <div class="w-9 h-9 bg-white/5 rounded-xl flex items-center justify-center text-emerald-400">
                        <i data-feather="clock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">Node History</p>
                        <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest">Registration timeline</p>
                    </div>
                </div>
                <div class="relative z-10 space-y-4">
                    <div>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Created At</p>
                        <p class="text-sm font-black text-white">{{ $warehouse->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Last Updated</p>
                        <p class="text-sm font-black text-emerald-400">{{ $warehouse->updated_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
@endsection
