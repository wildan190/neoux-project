@extends('layouts.app', [
    'title' => 'Edit Product',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Catalogue', 'url' => route('catalogue.index')],
        ['name' => $product->name, 'url' => route('catalogue.show', $product)],
        ['name' => 'Edit', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-[1600px] mx-auto">
    <div class="lg:grid lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left Column: Product Status --}}
        <div class="hidden lg:block lg:col-span-3 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 mb-6">
                    <i data-feather="edit-3" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-4">Editing Asset</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-6">Updating this product will reflect across all variants and active marketplace listings immediately.</p>
                
                <div class="space-y-4 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-gray-400">
                        <span>Last Updated</span>
                        <span class="text-gray-900 dark:text-white">{{ $product->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-gray-400">
                        <span>Variants</span>
                        <span class="text-gray-900 dark:text-white">{{ $product->items_count ?? $product->items->count() }} SKUs</span>
                    </div>
                </div>
            </div>

            <div class="bg-amber-500 rounded-[2.5rem] p-8 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <h4 class="text-sm font-black uppercase tracking-widest mb-2 relative z-10">Visibility</h4>
                <p class="text-xs text-amber-50 leading-relaxed relative z-10">Disabling the 'Active' status will hide this product and all its SKUs from the public marketplace.</p>
            </div>
        </div>

        {{-- Middle Column: The Form --}}
        <div class="lg:col-span-6">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-8 md:p-12">
                    <div class="flex items-center justify-between mb-10">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg">
                                <i data-feather="package" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Edit Product</h2>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Global Asset ID: {{ substr($product->id, 0, 8) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-black {{ $product->is_active ? 'text-emerald-500' : 'text-gray-400' }} uppercase tracking-widest">
                                {{ $product->is_active ? 'LIVE' : 'DRAFT' }}
                            </span>
                            <div class="w-2 h-2 rounded-full {{ $product->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-300' }}"></div>
                        </div>
                    </div>

                    <form action="{{ route('catalogue.update', $product) }}" method="POST" class="space-y-10">
                        @csrf
                        @method('PUT')
                        
                        {{-- General Info Section --}}
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="w-8 h-px bg-gray-100 dark:bg-gray-800"></span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Product Specifications</span>
                                <span class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="category_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Category</label>
                                    <select name="category_id" id="category_id" 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5" 
                                        required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Product Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5" 
                                        required>
                                    @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="brand" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Brand</label>
                                    <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand) }}" 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5">
                                    @error('brand') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex items-center justify-end">
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <div class="relative w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500 shadow-inner"></div>
                                        <span class="ms-4 text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Marketplace Active</span>
                                    </label>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Detailed Description</label>
                                    <textarea name="description" id="description" rows="6" 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5">{{ old('description', $product->description) }}</textarea>
                                    @error('description') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-10 flex items-center justify-between gap-6 border-t border-gray-100 dark:border-gray-800">
                            <a href="{{ route('catalogue.show', $product) }}" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-900 dark:hover:text-white transition-colors">
                                Discard Changes
                            </a>
                            <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-indigo-500/30 transition-all hover:-translate-y-1">
                                Save Product Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Preview/Context --}}
        <div class="hidden lg:block lg:col-span-3 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden sticky top-8">
                <div class="p-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Marketplace Presence</h3>
                    
                    <div class="space-y-6">
                        @if($product->items->count() > 0)
                            <div class="aspect-square bg-gray-50 dark:bg-gray-900 rounded-[2rem] flex items-center justify-center relative overflow-hidden group">
                                @if($product->items->first()->primaryImage)
                                    <img src="{{ asset('storage/' . $product->items->first()->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                @else
                                    <i data-feather="image" class="w-12 h-12 text-gray-200"></i>
                                @endif
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <a href="{{ route('catalogue.show', $product) }}" class="bg-white text-gray-900 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl">View Details</a>
                                </div>
                            </div>
                        @endif
                        
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Pricing Strategy</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white uppercase">
                                    @php
                                        $prices = $product->items->pluck('price');
                                        $min = $prices->min();
                                        $max = $prices->max();
                                    @endphp
                                    @if($min == $max)
                                        Rp {{ number_format($min, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($min, 0, ',', '.') }} - {{ number_format($max, 0, ',', '.') }}
                                    @endif
                                </p>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Stock Distribution</p>
                                <p class="text-xs font-black text-emerald-500 uppercase">{{ number_format($product->items->sum('stock')) }} Total Units</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
