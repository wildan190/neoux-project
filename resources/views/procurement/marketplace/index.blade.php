@extends('layouts.app', [
    'title' => 'Marketplace',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Procurement', 'url' => '#'],
        ['name' => 'Marketplace', 'url' => '#']
    ]
])

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    {{-- Sidebar Filters --}}
    <div class="w-full lg:w-64 flex-shrink-0 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Categories</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('procurement.marketplace.index') }}" class="flex items-center text-sm {{ !request('category') ? 'text-primary-600 font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-primary-600' }}">
                        All Products
                    </a>
                </li>
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('procurement.marketplace.index', ['category' => $category->id]) }}" class="flex items-center text-sm {{ request('category') == $category->id ? 'text-primary-600 font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-primary-600' }}">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        
        {{-- Price Filter Placeholder --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700 hidden lg:block">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Filter</h3>
            <form method="GET">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="space-y-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product..." class="w-full rounded-lg border-gray-300 text-sm dark:bg-gray-700 dark:border-gray-600">
                    <button type="submit" class="w-full py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition">Apply</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1">
        {{-- Header / Search --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6 border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row gap-4 items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white hidden md:block">Marketplace</h2>
            
            <div class="flex-1 w-full md:max-w-xl">
                <form method="GET" class="relative">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <i data-feather="search" class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products in Marketplace..." class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600">
                </form>
            </div>

            <a href="{{ route('procurement.marketplace.cart') }}" class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 transition">
                <i data-feather="shopping-cart" class="w-6 h-6"></i>
                @if(session('marketplace_cart') && count(session('marketplace_cart')) > 0)
                    <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white dark:border-gray-800">
                        {{ count(session('marketplace_cart')) }}
                    </span>
                @endif
            </a>
        </div>

        {{-- Product Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($products as $product)
                <a href="{{ route('procurement.marketplace.show', $product) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-100 dark:border-gray-700 overflow-hidden group flex flex-col h-full">
                    {{-- Image --}}
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
                        @php
                            $firstItem = $product->items->first();
                            $image = $firstItem ? $firstItem->primaryImage : null;
                            $minPrice = $product->items->min('price');
                        @endphp
                        
                        @if($image)
                             <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.src='{{ asset('assets/img/products/default-product.png') }}'">
                        @else
                            <img src="{{ asset('assets/img/products/default-product.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-50">
                        @endif
                    </div>
                    
                    {{-- Details --}}
                    <div class="p-3 flex flex-col flex-1">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 mb-1 group-hover:text-primary-600 transition-colors h-[40px]">{{ $product->name }}</h3>
                        
                        <div class="mt-auto">
                            <div class="text-base font-bold text-gray-900 dark:text-white">
                                @if($minPrice)
                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                @else
                                    <span class="text-xs text-gray-500">Check Detail</span>
                                @endif
                            </div>
                            
                            {{-- Location / Brand Mockup like Tokopedia --}}
                            <div class="flex items-center gap-1 mt-2 text-xs text-gray-500">
                                <i data-feather="map-pin" class="w-3 h-3"></i>
                                <span class="truncate">{{ $product->company->name ?? 'Unknown' }} - {{ $product->company->address ?? 'No Address' }}</span>
                            </div>
                            
                            {{-- Rating Mockup --}}
                            <div class="flex items-center gap-1 mt-1">
                                <i data-feather="star" class="w-3 h-3 text-yellow-400 fill-current"></i>
                                <span class="text-xs text-gray-500">4.8 | Sold 100+</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-700 mb-4">
                        <i data-feather="search" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Product not found</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400 text-sm">Try different keywords or filters.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection
