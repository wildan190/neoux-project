@extends('layouts.app', [
    'title' => 'Register Storage Point',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => url('/')],
        ['name' => 'Storage Points', 'url' => route('procurement.warehouse.index')],
        ['name' => 'New Unit', 'url' => null],
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Page Header --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 p-10 text-emerald-500/5 pointer-events-none">
            <i data-feather="archive" style="width:180px;height:180px;"></i>
        </div>
        <div class="relative z-10 flex items-center gap-3 mb-4">
            <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">Configuration</span>
            <div class="h-px w-8 bg-gray-200 dark:bg-gray-700"></div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Warehouse Module</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2 relative z-10">
            Register <span class="text-emerald-600">Storage Unit</span>
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium relative z-10">
            Define a new physical or logical storage location for incoming goods and inventory management.
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
                            <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 leading-relaxed">The official name of the storage facility. E.g. <span class="font-black text-gray-700 dark:text-gray-300">MAIN WAREHOUSE</span>.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center shrink-0 mt-0.5">
                            <i data-feather="hash" class="w-3.5 h-3.5 text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">Reference Code</p>
                            <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 leading-relaxed">A unique short code used in tracking. E.g. <span class="font-black text-gray-700 dark:text-gray-300">WH-A1</span>.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center shrink-0 mt-0.5">
                            <i data-feather="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">Delivery Address</p>
                            <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 leading-relaxed">Full physical address for delivery routing.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Tips --}}
            <div class="bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <i data-feather="zap" class="w-4 h-4 text-emerald-600"></i>
                    <p class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">Quick Tips</p>
                </div>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                        <p class="text-[10px] font-medium text-emerald-700 dark:text-emerald-400/80 leading-relaxed">Reference codes must be unique.</p>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                        <p class="text-[10px] font-medium text-emerald-700 dark:text-emerald-400/80 leading-relaxed">QR codes can be generated after creation.</p>
                    </li>
                </ul>
            </div>
        </div>

        {{-- CENTER: Main Form --}}
        <div class="lg:col-span-6">
            <form action="{{ route('procurement.warehouse.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-2 pb-4 border-b border-gray-50 dark:border-gray-800">
                        <div class="w-9 h-9 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-feather="edit-3" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Storage Details</p>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Fill in all required fields</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Unit Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                               placeholder="e.g. MAIN WAREHOUSE"
                               class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                        @error('name') <p class="text-[10px] text-red-500 font-bold uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="code" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Reference Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" required value="{{ old('code') }}"
                               placeholder="e.g. WH-A1"
                               class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none">
                        @error('code') <p class="text-[10px] text-red-500 font-bold uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="address" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Delivery Address / Location</label>
                        <textarea name="address" id="address" rows="4"
                                  placeholder="Full physical address for delivery logistics..."
                                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none resize-none">{{ old('address') }}</textarea>
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
                        Authorize Storage Unit
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT: Status / Info Panel --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Warehouse Stats --}}
            <div class="bg-gray-900 dark:bg-black rounded-2xl border border-gray-800 p-6 text-white relative overflow-hidden">
                <div class="absolute bottom-0 right-0 -mb-10 -mr-10 w-40 h-40 bg-emerald-600/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/5 relative z-10">
                    <div class="w-9 h-9 bg-white/5 rounded-xl flex items-center justify-center text-emerald-400">
                        <i data-feather="layers" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">Network Status</p>
                        <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest">Current warehouse nodes</p>
                    </div>
                </div>
                @php
                    $existingWarehouses = \Modules\Company\Models\Warehouse::where('company_id', session('selected_company_id'))->count();
                @endphp
                <div class="relative z-10 space-y-4">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Active Nodes</p>
                            <p class="text-4xl font-black text-white">{{ $existingWarehouses }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Adding</p>
                            <p class="text-2xl font-black text-emerald-400">+1</p>
                        </div>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-1.5">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ min(100, ($existingWarehouses + 1) * 10) }}%"></div>
                    </div>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">After registration: {{ $existingWarehouses + 1 }} storage node(s)</p>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div class="w-9 h-9 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                        <i data-feather="check-square" class="w-4 h-4"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Pre-Registration Checklist</p>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Warehouse has a distinct physical location',
                        'Reference code is ready and unique',
                        'Address is complete for logistics',
                        'Permission to register confirmed',
                    ] as $check)
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                            <i data-feather="check" class="w-3 h-3 text-emerald-600"></i>
                        </div>
                        <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400">{{ $check }}</p>
                    </li>
                    @endforeach
                </ul>
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
