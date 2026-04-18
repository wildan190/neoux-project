<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huntr — Solusi ERP Modern</title>

    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
    <link href="https://unpkg.com/aos@next/dist/aos.css" rel="stylesheet">
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-all duration-300 min-h-screen flex flex-col pt-24">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 right-0 bg-white/70 dark:bg-gray-900/70 backdrop-blur z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-extrabold text-primary-600 dark:text-primary-400">Huntr</a>

            <div class="hidden md:flex flex-1 justify-center px-8">
                <form action="/" method="GET" class="w-full max-w-lg relative">
                    <i data-feather="search" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk..." class="w-full pl-12 pr-4 py-2.5 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 transition-all text-sm outline-none shadow-sm text-gray-900 dark:text-white">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                </form>
            </div>

            <div>
                <a href="/login" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow font-medium">
                    Login / Masuk
                </a>
            </div>
        </div>
    </nav>

    {{-- MOBILE SEARCH (Visible only on small screens) --}}
    <div class="md:hidden px-6 mb-6">
        <form action="/" method="GET" class="relative">
            <i data-feather="search" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk..." class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
        </form>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-6" id="products">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-2">Eksplorasi <span class="text-primary-600 dark:text-primary-400">Produk</span></h1>
            <p class="text-gray-600 dark:text-gray-400">Temukan berbagai ragam produk berkualitas dari partner terpercaya kami.</p>
        </div>

        {{-- CATEGORIES MENU --}}
        @if(isset($categories) && $categories->count() > 0)
            <div class="mb-10 bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Kategori</h2>
                
                <div id="categoryGrid" class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-3 overflow-hidden transition-all duration-500 relative" style="max-height: 90px;">
                    <!-- Semua Kategori -->
                    <a href="/" class="flex flex-col items-center gap-1.5 group">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center transition-all group-hover:scale-110 {{ !request('category') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/30' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/40 group-hover:text-primary-600 dark:group-hover:text-primary-400' }}">
                            <i data-feather="grid" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[10px] font-semibold text-center line-clamp-1 {{ !request('category') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400' }}">Semua</span>
                    </a>
                    
                    @foreach($categories as $cat)
                        @php
                            $icon = 'box';
                            $name = strtolower($cat->name);
                            if (str_contains($name, 'elektronik') || str_contains($name, 'electronic') || str_contains($name, 'it')) $icon = 'cpu';
                            elseif (str_contains($name, 'laptop') || str_contains($name, 'komputer') || str_contains($name, 'pc')) $icon = 'laptop';
                            elseif (str_contains($name, 'mobil') || str_contains($name, 'motor') || str_contains($name, 'kendaraan')) $icon = 'truck';
                            elseif (str_contains($name, 'office') || str_contains($name, 'kantor') || str_contains($name, 'atk')) $icon = 'briefcase';
                            elseif (str_contains($name, 'furnitur') || str_contains($name, 'meja') || str_contains($name, 'kursi')) $icon = 'server';
                            elseif (str_contains($name, 'jasa') || str_contains($name, 'service') || str_contains($name, 'layanan')) $icon = 'tool';
                            elseif (str_contains($name, 'medis') || str_contains($name, 'kesehatan') || str_contains($name, 'alat ukur')) $icon = 'heart';
                            elseif (str_contains($name, 'makanan') || str_contains($name, 'minuman') || str_contains($name, 'konsumsi')) $icon = 'coffee';
                            elseif (str_contains($name, 'pakaian') || str_contains($name, 'baju') || str_contains($name, 'seragam')) $icon = 'shopping-bag';
                        @endphp
                        
                        <a href="/?category={{ $cat->slug }}{{ request('search') ? '&search='.request('search') : '' }}" class="flex flex-col items-center gap-1.5 group">
                            <div class="w-11 h-11 rounded-full flex items-center justify-center transition-all group-hover:scale-110 {{ request('category') == $cat->slug ? 'bg-primary-600 text-white shadow-md shadow-primary-600/30' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/40 group-hover:text-primary-600 dark:group-hover:text-primary-400' }}">
                                <i data-feather="{{ $icon }}" class="w-4 h-4"></i>
                            </div>
                            <span class="text-[10px] font-semibold text-center line-clamp-1 {{ request('category') == $cat->slug ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400' }}">{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>

                @if($categories->count() > 7)
                <div class="mt-4 text-center">
                    <button type="button" onclick="toggleCategories()" id="toggleCategoryBtn" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 bg-primary-50 dark:bg-primary-900/30 px-4 py-2 rounded-full transition-colors">
                        <span>Lihat Semua Kategori</span>
                        <i data-feather="chevron-down" class="w-4 h-4" id="toggleCategoryIcon"></i>
                    </button>
                </div>
                
                <script>
                    function toggleCategories() {
                        const grid = document.getElementById('categoryGrid');
                        const btnText = document.querySelector('#toggleCategoryBtn span');
                        const icon = document.getElementById('toggleCategoryIcon');
                        
                        if (grid.style.maxHeight === '90px' || grid.style.maxHeight === '') {
                            grid.style.maxHeight = '2000px';
                            btnText.innerText = 'Sembunyikan Kategori';
                            icon.setAttribute('data-feather', 'chevron-up');
                        } else {
                            grid.style.maxHeight = '90px';
                            btnText.innerText = 'Lihat Semua Kategori';
                            icon.setAttribute('data-feather', 'chevron-down');
                        }
                        feather.replace();
                    }
                </script>
                @endif
            </div>
        @endif

        @if(request()->filled('search'))
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400">Menampilkan hasil pencarian untuk: <span class="font-bold text-gray-900 dark:text-white">"{{ request('search') }}"</span></p>
            </div>
        @endif

        {{-- PRODUCT GRID --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
            @forelse($featuredProducts as $item)
                <!-- Product Card -->
                <a href="{{ route('market.show', $item->id) }}" data-aos="fade-up" data-aos-delay="{{ ($loop->iteration % 8) * 50 }}" class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col block">
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
                    <p class="text-gray-500 max-w-md mx-auto">Kami tidak menemukan produk yang cocok dengan pencarian atau kategori ini.</p>
                    <a href="/" class="mt-6 inline-flex px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">Lihat Semua Produk</a>
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        <div class="mt-12 mb-16">
            @if(method_exists($featuredProducts, 'links'))
                {{ $featuredProducts->appends(request()->query())->links() }}
            @endif
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="mt-auto bg-gray-900 dark:bg-gray-950 text-gray-400 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">
                {{-- Brand --}}
                <div class="md:col-span-2">
                    <a href="/" class="text-2xl font-extrabold text-white mb-4 inline-block">Huntr</a>
                    <p class="text-sm leading-relaxed max-w-sm">Platform e-Procurement & e-Catalogue terintegrasi untuk mendukung efisiensi pengadaan dan operasional bisnis Anda.</p>
                    <div class="flex gap-3 mt-5">
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-primary-600 flex items-center justify-center transition-colors"><i data-feather="instagram" class="w-4 h-4 text-gray-400 hover:text-white"></i></a>
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-primary-600 flex items-center justify-center transition-colors"><i data-feather="linkedin" class="w-4 h-4 text-gray-400 hover:text-white"></i></a>
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-primary-600 flex items-center justify-center transition-colors"><i data-feather="mail" class="w-4 h-4 text-gray-400 hover:text-white"></i></a>
                    </div>
                </div>

                {{-- Links --}}
                <div>
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Navigasi</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="/" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('market.index') }}" class="hover:text-white transition-colors">Marketplace</a></li>
                        <li><a href="/login" class="hover:text-white transition-colors">Login</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Kontak</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li class="flex items-center gap-2"><i data-feather="mail" class="w-4 h-4 text-primary-500"></i> hello@huntr.id</li>
                        <li class="flex items-center gap-2"><i data-feather="phone" class="w-4 h-4 text-primary-500"></i> +62 812 3456 7890</li>
                        <li class="flex items-center gap-2"><i data-feather="map-pin" class="w-4 h-4 text-primary-500"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
                <p>&copy; {{ date('Y') }} Huntr. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 900, once: true });
        feather.replace();
    </script>

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</body>
</html>