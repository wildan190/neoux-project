@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col pt-24 pb-12">
    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-0 bg-white/70 dark:bg-gray-900/70 backdrop-blur z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-extrabold text-primary-600 dark:text-primary-400">Huntr</a>

            <div class="hidden md:flex flex-1 justify-center px-8">
                <form action="{{ route('market.index') }}" method="GET" class="w-full max-w-lg relative">
                    <i data-feather="search" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk..." class="w-full pl-12 pr-4 py-2.5 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm outline-none shadow-sm text-gray-900 dark:text-white">
                </form>
            </div>

            <div>
                <a href="/login" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow font-medium transition-colors">
                    Login / Masuk
                </a>
            </div>
        </div>
    </nav>

    {{-- MOBILE SEARCH (Visible only on small screens) --}}
    <div class="md:hidden px-6 mb-6">
        <form action="{{ route('market.index') }}" method="GET" class="relative">
            <i data-feather="search" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk..." class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white">
        </form>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-6">
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Marketplace <span class="text-primary-600 dark:text-primary-400">e-Catalogue</span></h1>
            <p class="text-gray-600 dark:text-gray-400">Temukan berbagai ragam produk berkualitas dari partner terpercaya kami.</p>
        </div>

        @if(request()->filled('search'))
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400">Menampilkan hasil pencarian untuk: <span class="font-bold text-gray-900 dark:text-white">"{{ request('search') }}"</span></p>
            </div>
        @endif

        {{-- PRODUCT GRID --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
            @forelse($products as $item)
                <!-- Product Card -->
                <a href="{{ route('market.show', $item->id) }}" class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col block">
                    <!-- Image -->
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                        @if($item->primaryImage)
                                <img src="{{ $item->primaryImage->url }}" alt="{{ $item->product?->name ?? 'Product' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i data-feather="image" class="w-12 h-12"></i>
                                </div>
                        @endif
                        <div class="absolute top-3 right-3 bg-white/90 dark:bg-gray-900/90 backdrop-blur text-xs font-bold px-3 py-1.5 rounded-full shadow-sm text-gray-700 dark:text-gray-200">
                            {{ $item->unit ?? 'Pcs' }}
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col">

                        <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2 line-clamp-2 leading-tight group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                            {{ $item->product?->name ?? $item->name ?? 'Untitled Product' }}
                        </h3>
                        <div class="mt-auto pt-4 flex items-center justify-between border-t border-gray-50 dark:border-gray-700/50">
                            <div></div>
                            <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white dark:group-hover:bg-primary-600 dark:group-hover:text-white transition-colors">
                                <i data-feather="arrow-right" class="w-5 h-5"></i>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-20 text-center bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-50 dark:bg-gray-700 rounded-full flex justify-center items-center">
                        <i data-feather="search" class="w-10 h-10 text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Produk Tidak Ditemukan</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Kami tidak menemukan produk yang cocok dengan pencarian Anda. Silakan cari menggunakan kata kunci lain.</p>
                    <a href="{{ route('market.index') }}" class="mt-6 inline-flex px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">Lihat Semua Produk</a>
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="mt-auto pt-16 pb-8 text-center text-gray-500 dark:text-gray-400">
        <p>&copy; {{ date('Y') }} Huntr e-Catalogue. Mendorong efisiensi bisnis Anda.</p>
    </footer>
</div>
@endsection
