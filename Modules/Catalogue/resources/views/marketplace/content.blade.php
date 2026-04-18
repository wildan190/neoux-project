<div id="products">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-2 tracking-tight">Eksplorasi <span class="text-primary-600 dark:text-primary-400">Produk</span></h1>
        <p class="text-gray-600 dark:text-gray-400">Temukan berbagai ragam produk berkualitas dari partner terpercaya kami.</p>
    </div>

    {{-- REFINED SEARCH BAR (SCOUT READY) --}}
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-1 md:p-2 shadow-lg shadow-gray-200/50 dark:shadow-black/50 border border-gray-100 dark:border-gray-800 transition-all group hover:border-primary-500/30 w-full max-w-4xl mx-auto">
            <form action="{{ url('/') }}" method="GET" class="relative flex flex-col md:flex-row gap-1">
                <div class="flex-1 relative">
                    <i data-feather="search" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-600 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari kebutuhan procurement..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 dark:bg-gray-900/50 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-sm font-semibold text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-600 outline-none">
                    
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                </div>
                
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-primary-600/20 active:scale-95 flex items-center justify-center gap-2">
                    <i data-feather="search" class="w-3 h-3"></i>
                    <span>Cari</span>
                </button>
            </form>
        </div>

        {{-- Quick Tags for better UX --}}
        @if(isset($categories) && $categories->count() > 0)
            <div class="mt-6 flex flex-wrap gap-2 px-6">
                @foreach($categories->take(5) as $cat)
                    <a href="/?category={{ $cat->slug }}" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-primary-600 transition-colors">
                        #{{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @if(request()->filled('search'))
        <div class="mb-8">
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Hasil pencarian untuk: <span class="text-primary-600 dark:text-primary-400">"{{ request('search') }}"</span></p>
        </div>
    @endif

    @php
        $marketProducts = $products ?? $featuredProducts ?? [];
    @endphp

    {{-- PRODUCT GRID --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
        @forelse($marketProducts as $item)
            <!-- Product Card -->
            <a href="{{ route('market.show', $item->id) }}" class="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden flex flex-col h-full">
                <!-- Image -->
                <div class="relative aspect-square overflow-hidden bg-gray-50 dark:bg-gray-900/50">
                    @if($item->primaryImage)
                            <img src="{{ $item->primaryImage->url }}" alt="{{ $item->product?->name ?? 'Product' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-700">
                                <i data-feather="image" class="w-12 h-12"></i>
                            </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-white/95 dark:bg-gray-900/95 backdrop-blur text-[10px] font-black px-3 py-1.5 rounded-xl shadow-lg text-gray-600 dark:text-gray-300 uppercase tracking-widest border border-gray-100 dark:border-gray-800">
                        {{ $item->unit ?? 'UNIT' }}
                    </div>
                </div>
                <!-- Content -->
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base mb-3 line-clamp-2 leading-snug group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        {{ $item->product?->name ?? $item->name ?? 'Untitled Product' }}
                    </h3>
                    
                    <div class="mt-auto pt-5 flex items-center justify-between border-t border-gray-50 dark:border-gray-800/50">
                        <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Lihat Detail</span>
                        <div class="w-10 h-10 rounded-2xl bg-gray-50 dark:bg-gray-900/80 text-gray-400 flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <i data-feather="arrow-right" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-24 text-center bg-white dark:bg-gray-800 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-gray-800">
                <div class="w-24 h-24 mx-auto mb-8 bg-gray-50 dark:bg-gray-700/50 rounded-3xl flex justify-center items-center">
                    <i data-feather="package" class="w-12 h-12 text-gray-300"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3 tracking-tight">Belum Ada Produk</h3>
                <p class="text-gray-500 max-w-sm mx-auto text-sm leading-relaxed">Produk tidak ditemukan untuk kriteria pencarian ini. Coba kriteria lain atau kembali ke beranda.</p>
                <a href="/" class="mt-8 inline-flex px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white text-xs font-black rounded-2xl transition-all shadow-xl shadow-primary-600/20 uppercase tracking-widest">Segarkan Halaman</a>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="mt-16 mb-20 flex justify-center">
        @if(isset($marketProducts) && method_exists($marketProducts, 'links'))
            <div class="bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                {{ $marketProducts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
