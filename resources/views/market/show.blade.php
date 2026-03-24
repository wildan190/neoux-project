@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col pt-24 pb-12 bg-white dark:bg-gray-900">
    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-0 bg-white/70 dark:bg-gray-900/70 backdrop-blur z-50 border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-extrabold text-primary-600 dark:text-primary-400">Huntr</a>

            <div class="hidden md:flex flex-1 justify-center px-8">
                <form action="{{ route('market.index') }}" method="GET" class="w-full max-w-lg relative">
                    <i data-feather="search" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" placeholder="Cari produk..." class="w-full pl-12 pr-4 py-2.5 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none shadow-sm text-gray-900 dark:text-white">
                </form>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('market.index') }}" class="hidden md:block font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Kembali ke Market</a>
                <a href="/login" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow font-medium transition-colors">
                    Login / Masuk
                </a>
            </div>
        </div>
    </nav>

    {{-- BREADCRUMBS --}}
    <div class="max-w-7xl mx-auto px-6 w-full mb-8 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <a href="/" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
        <i data-feather="chevron-right" class="w-4 h-4"></i>
        <a href="{{ route('market.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Marketplace</a>
        <i data-feather="chevron-right" class="w-4 h-4"></i>
        <span class="text-gray-900 dark:text-white font-medium truncate max-w-xs">{{ $product->product?->name ?? $product->name }}</span>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-6">
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-16">
            <div class="grid md:grid-cols-2 gap-0">
                
                {{-- PRODUCT IMAGES --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700">
                    <div class="w-full max-w-md aspect-square rounded-2xl overflow-hidden bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 mb-6 relative">
                        @if($product->primaryImage)
                             <img src="{{ $product->primaryImage->url }}" alt="{{ $product->product?->name }}" class="w-full h-full object-contain p-4" id="main-product-image">
                        @else
                             <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600">
                                 <i data-feather="image" class="w-24 h-24"></i>
                             </div>
                        @endif
                    </div>

                    @if($product->images->count() > 1)
                        <div class="flex gap-4 overflow-x-auto w-full max-w-md px-2 pb-2 hide-scrollbar">
                            @foreach($product->images as $img)
                                <button onclick="document.getElementById('main-product-image').src='{{ $img->url }}'" class="w-20 h-20 flex-shrink-0 rounded-xl border-2 {{ $img->is_primary ? 'border-primary-500' : 'border-gray-200 dark:border-gray-700' }} overflow-hidden bg-white dark:bg-gray-800 hover:border-primary-400 focus:outline-none transition-colors">
                                    <img src="{{ $img->url }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- PRODUCT DETAILS --}}
                <div class="p-8 md:p-12 flex flex-col">
                    <div class="inline-flex max-w-max items-center gap-2 px-3 py-1.5 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-bold uppercase tracking-wider mb-4 border border-primary-100 dark:border-primary-800/50">
                        <i data-feather="briefcase" class="w-3.5 h-3.5"></i>
                        {{ $product->company?->name ?? 'Vendor Internal' }}
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-2 leading-tight">
                        {{ $product->product?->name ?? $product->name }}
                    </h1>
                    
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        @if($product->product?->category)
                            Kategori: <span class="font-medium text-gray-900 dark:text-gray-200">{{ $product->product->category->name }}</span> • 
                        @endif
                        SKU: <span class="font-medium px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">{{ $product->sku }}</span>
                    </div>

                    <div class="text-4xl font-black text-gray-900 dark:text-white mb-8 py-6 border-y border-gray-100 dark:border-gray-700/50">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                        <span class="text-lg font-medium text-gray-500 font-normal">/ {{ $product->unit }}</span>
                    </div>

                    <div class="mb-8">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-lg">Deskripsi Produk</h3>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed">
                            {!! nl2br(e($product->product?->description ?? $product->description ?? 'Tidak ada deskripsi tersedia.')) !!}
                        </div>
                    </div>

                    @if($product->attributes && $product->attributes->count() > 0)
                        <div class="mb-8">
                            <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-lg">Spesifikasi Tambahan</h3>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach($product->attributes as $attr)
                                    <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $attr->name }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $attr->value }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-auto pt-6 flex flex-col sm:flex-row gap-4">
                        <a href="/login?redirect={{ urlencode(url()->current()) }}" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                            <i data-feather="shopping-cart" class="w-5 h-5"></i>
                            Pesan Sekarang via e-Procurement
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- RELATED PRODUCTS --}}
        @if($relatedProducts && $relatedProducts->count() > 0)
            <div class="mb-16">
                <div class="flex justify-between items-end mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Produk Lainnya dari <span class="text-primary-600">{{ $product->company?->name }}</span></h2>
                    <a href="{{ route('market.index', ['search' => $product->company?->name]) }}" class="text-primary-600 hover:underline font-medium text-sm">Lihat Semua Vendor</a>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('market.show', $related->id) }}" class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                            <div class="relative aspect-square overflow-hidden bg-gray-50 dark:bg-gray-700 p-4 flex items-center justify-center">
                                @if($related->primaryImage)
                                    <img src="{{ $related->primaryImage->url }}" alt="{{ $related->product?->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <i data-feather="image" class="w-8 h-8 text-gray-300"></i>
                                @endif
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1 line-clamp-2 leading-tight group-hover:text-primary-600 transition-colors">
                                    {{ $related->product?->name ?? $related->name ?? 'Untitled' }}
                                </h3>
                                <div class="mt-auto pt-2 font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($related->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection
