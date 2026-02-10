@extends('layouts.app', [
    'title' => 'Marketplace',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Procurement', 'url' => '#'],
        ['name' => 'Marketplace', 'url' => '#']
    ]
])

@section('content')
<div class="space-y-10 pb-20">
    {{-- Hero Search Section --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-primary-600 via-primary-700 to-indigo-800 p-8 md:p-16 text-white shadow-2xl shadow-primary-500/20">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 h-64 w-64 rounded-full bg-primary-400/20 blur-3xl"></div>
        
        <div class="relative z-10 max-w-2xl mx-auto text-center space-y-6">
            <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-tight">
                Discover Your Next <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-200 to-white">Procurement</span> Success.
            </h1>
            <p class="text-primary-100 text-lg font-medium opacity-90">Browse through thousands of high-quality products curated for your business needs.</p>
            
            <form method="GET" class="relative group mt-8">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative flex items-center">
                    <i data-feather="search" class="absolute left-6 w-6 h-6 text-primary-400 group-focus-within:text-primary-600 transition-colors z-20"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search for items, brands, or categories..." 
                        class="w-full pl-16 pr-32 py-5 rounded-2xl bg-white/95 backdrop-blur-md border-0 text-gray-900 placeholder-gray-400 focus:ring-4 focus:ring-white/20 shadow-xl text-lg transition-all z-10">
                    <button type="submit" class="absolute right-3 py-3 px-8 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold shadow-lg transition-all active:scale-95 z-20">
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Category Navigation --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Explore Categories</h2>
            <a href="{{ route('procurement.marketplace.index') }}" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition">View All</a>
        </div>
        
        {{-- Horizontal Scrollable Categories --}}
        <div class="flex overflow-x-auto pb-4 gap-4 no-scrollbar -mx-4 px-4 scroll-smooth">
            <a href="{{ route('procurement.marketplace.index') }}" 
                class="flex-shrink-0 px-6 py-3 rounded-2xl border-2 transition-all font-bold text-sm flex items-center gap-2
                {{ !request('category') ? 'bg-primary-600 border-primary-600 text-white shadow-lg shadow-primary-500/20' : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-primary-200' }}">
                <i data-feather="grid" class="w-4 h-4"></i>
                All Products
            </a>
            @foreach($categories as $category)
                <a href="{{ route('procurement.marketplace.index', ['category' => $category->id]) }}" 
                    class="flex-shrink-0 px-6 py-3 rounded-2xl border-2 transition-all font-bold text-sm flex items-center gap-2
                    {{ request('category') == $category->id ? 'bg-primary-600 border-primary-600 text-white shadow-lg shadow-primary-500/20' : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-primary-200' }}">
                    <i data-feather="tag" class="w-4 h-4"></i>
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Product Listing Container --}}
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Sidebar for Desktop --}}
        <div class="hidden lg:block w-72 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Marketplace Stats</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl flex items-center justify-center text-indigo-600">
                            <i data-feather="package" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Available Items</p>
                            <p class="text-xl font-black text-gray-900 dark:text-white">{{ $products->total() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl shadow-gray-900/10">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i data-feather="shopping-cart" class="w-20 h-20 -mr-4 -mt-4"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">Need a Cart?</h3>
                <p class="text-sm text-gray-400 mb-6">Check your items before creating a purchase requisition.</p>
                <a href="{{ route('procurement.marketplace.cart') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold transition">
                    View My Cart
                    <i data-feather="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 space-y-8">
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6">
                @forelse($products as $product)
                    <a href="{{ route('procurement.marketplace.show', $product) }}" class="group block relative bg-white dark:bg-gray-800 rounded-2xl md:rounded-[2rem] border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl hover:shadow-primary-500/10 transition-all duration-500 hover:-translate-y-2">
                        {{-- Image Wrapper --}}
                        <div class="aspect-[4/5] relative overflow-hidden bg-gray-50 dark:bg-gray-900">
                            @php
                                $firstItem = $product->items->first();
                                $image = $firstItem ? $firstItem->primaryImage : null;
                            @endphp
                            
                            @if($image)
                                 <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" onerror="this.src='{{ asset('assets/img/products/default-product.png') }}'">
                            @else
                                <img src="{{ asset('assets/img/products/default-product.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-50">
                            @endif

                            {{-- Float Badge --}}
                            <div class="absolute top-2 left-2 md:top-4 md:left-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md px-2 py-0.5 md:px-3 md:py-1 rounded-full border border-white/20 shadow-sm transition-opacity group-hover:opacity-0">
                                <span class="text-[8px] md:text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300">
                                    {{ $product->category->name ?? 'General' }}
                                </span>
                            </div>

                            {{-- Quick Action Overlay --}}
                            <div class="absolute inset-0 bg-primary-600/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>

                        {{-- Details --}}
                        <div class="p-3 md:p-6 space-y-2 md:space-y-4">
                            <div>
                                <h3 class="text-sm md:text-lg font-bold text-gray-900 dark:text-white line-clamp-2 leading-tight md:leading-snug group-hover:text-primary-600 transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-center gap-1 md:gap-2 mt-1 md:mt-2 text-[8px] md:text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <i data-feather="briefcase" class="w-2 md:w-3 h-2 md:h-3 text-primary-500"></i>
                                    <span class="truncate">{{ $product->company->name ?? 'Premium Vendor' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-1 md:gap-1.5">
                                    <div class="w-1.5 md:w-2 h-1.5 md:h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                                    <span class="text-[8px] md:text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-tighter">Ready</span>
                                </div>
                                <span class="text-[8px] md:text-[10px] font-black text-primary-600 uppercase tracking-[0.2em] opacity-0 group-hover:opacity-100 transition-opacity">
                                    Detail
                                </span>
                            </div>
                        </div>

                        {{-- Hover Bottom Info --}}
                        <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-white dark:from-gray-800 to-transparent translate-y-full group-hover:translate-y-0 transition-transform duration-300 pointer-events-none">
                            <span class="text-[10px] font-black text-primary-600 uppercase tracking-[0.2em]">View Specifications</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-20 text-center bg-white dark:bg-gray-800 rounded-[3rem] border border-dashed border-gray-200 dark:border-gray-700">
                        <div class="w-20 h-20 mx-auto mb-6 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center">
                            <i data-feather="search" class="w-10 h-10 text-gray-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Product not found</h3>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn't find anything matching your search. Try different keywords or filters.</p>
                        <a href="{{ route('procurement.marketplace.index') }}" class="mt-8 inline-flex items-center gap-2 px-8 py-3 bg-primary-600 text-white rounded-xl font-bold transition">Reset Search</a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="pt-8 border-t border-gray-100 dark:border-gray-700">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
