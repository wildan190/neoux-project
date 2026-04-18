@extends('catalogue::layouts.marketplace', [
    'title' => 'Marketplace',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Procurement', 'url' => '#'],
        ['name' => 'Marketplace', 'url' => '#']
    ]
])

@section('market-content')
<div class="space-y-10 pb-20">
    {{-- Modern Search Section --}}
    <div class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 -mx-4 px-4 py-10 md:py-16">
        <div class="max-w-4xl mx-auto text-center space-y-8">
            <div class="space-y-2">
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Find the Best <span class="text-primary-600 font-medium">Supplies</span> for Your Business
                </h1>
                <p class="text-gray-500 text-base md:text-lg font-medium">Reliable sourcing from verified premium vendors.</p>
            </div>
            
            <form method="GET" class="relative max-w-2xl mx-auto">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-feather="search" class="w-5 h-5 text-gray-400 group-focus-within:text-primary-600 transition-colors"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="What are you looking for today?" 
                        class="block w-full pl-11 pr-32 py-4 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm md:text-base">
                    <div class="absolute inset-y-1.5 right-1.5 flex">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-black tracking-widest uppercase transition-all active:scale-95 shadow-lg shadow-primary-600/20">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Category Circles --}}
    @guest
    <div class="space-y-6 py-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Shop by Category</h2>
            <a href="{{ route('procurement.marketplace.index') }}" class="text-[10px] font-bold text-primary-600 hover:underline uppercase tracking-widest">Reset Filter</a>
        </div>
        
        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-4 md:gap-6">
            <a href="{{ route('procurement.marketplace.index') }}" 
                class="group flex flex-col items-center gap-3 text-center">
                <div class="w-12 h-12 md:w-16 md:h-16 rounded-full flex items-center justify-center transition-all duration-300 shadow-sm
                    {{ !request('category') ? 'bg-primary-600 text-white ring-4 ring-primary-600/10' : 'bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-500 group-hover:border-primary-600 group-hover:text-primary-600' }}">
                    <i data-feather="grid" class="w-5 h-5 md:w-7 md:h-7"></i>
                </div>
                <span class="text-[10px] md:text-xs font-bold {{ !request('category') ? 'text-primary-600' : 'text-gray-600 dark:text-gray-400 group-hover:text-primary-600' }}">All</span>
            </a>
            @foreach($categories as $category)
                <a href="{{ route('procurement.marketplace.index', ['category' => $category->id]) }}" 
                    class="group flex flex-col items-center gap-3 text-center">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full flex items-center justify-center transition-all duration-300 shadow-sm
                        {{ request('category') == $category->id ? 'bg-primary-600 text-white ring-4 ring-primary-600/10' : 'bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-500 group-hover:border-primary-600 group-hover:text-primary-600' }}">
                        <i data-feather="tag" class="w-5 h-5 md:w-7 md:h-7"></i>
                    </div>
                    <span class="text-[10px] md:text-xs font-bold leading-tight line-clamp-1 {{ request('category') == $category->id ? 'text-primary-600' : 'text-gray-600 dark:text-gray-400 group-hover:text-primary-600' }}">
                        {{ $category->name }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
    @endguest
    {{-- Product Listing Container --}}
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Sidebar for Desktop --}}
        <div class="hidden lg:block w-72 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Market Statistics</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-primary-600">
                            <i data-feather="package" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Products</p>
                            <p class="text-lg font-black text-gray-900 dark:text-white">{{ $products->total() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-primary-600 rounded-2xl p-6 text-white relative overflow-hidden shadow-xl shadow-primary-600/20">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i data-feather="shopping-cart" class="w-16 h-16 -mr-2 -mt-2"></i>
                </div>
                <div class="relative z-10 space-y-4">
                    <h3 class="text-base font-black tracking-tight leading-tight">Ready to Source?</h3>
                    <p class="text-xs text-primary-100 font-medium leading-relaxed">View your selected items and generate requisitions instantly.</p>
                    <a href="{{ route('procurement.marketplace.cart') }}" class="inline-flex items-center justify-center w-full py-3 bg-white text-primary-600 rounded-xl text-xs font-black tracking-widest uppercase transition-all hover:bg-primary-50 active:scale-95">
                        MY CART
                    </a>
                </div>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5">
                @forelse($products as $product)
                    <a href="{{ route('procurement.marketplace.show', $product) }}" class="group block relative bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden hover:border-primary-500 transition-all duration-300">
                        {{-- Image Wrapper --}}
                        <div class="aspect-square relative overflow-hidden bg-gray-50 dark:bg-gray-900 border-b border-gray-50 dark:border-gray-800">
                            @php
                                $firstItem = $product->items->first();
                                $image = $firstItem ? $firstItem->primaryImage : null;
                            @endphp
                            
                            @if($image)
                                 <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='{{ asset('assets/img/products/default-product.png') }}'">
                            @else
                                <img src="{{ asset('assets/img/products/default-product.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-50">
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="p-3 md:p-4 space-y-2">
                            <h3 class="text-xs md:text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-tight min-h-[2rem]">
                                {{ $product->name }}
                            </h3>
                            
                            <div class="flex items-center gap-1.5">
                                <i data-feather="briefcase" class="w-3 h-3 text-gray-400"></i>
                                <span class="text-[9px] font-semibold text-gray-500 truncate uppercase tracking-widest">{{ $product->company->name ?? 'MEMBER VENDOR' }}</span>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                    <span class="text-[9px] font-bold text-green-600 uppercase tracking-widest">In Stock</span>
                                </div>
                                <div class="text-primary-600 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                    <i data-feather="chevron-right" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-20 text-center bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                        <i data-feather="search" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Product Not Found</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Try different keywords or reset filters.</p>
                        <a href="{{ route('procurement.marketplace.index') }}" class="mt-6 inline-flex px-8 py-3 bg-primary-600 text-white rounded-xl text-xs font-black tracking-widest uppercase transition-all shadow-lg shadow-primary-600/20">Reset Filters</a>
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
