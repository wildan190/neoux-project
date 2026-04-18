@extends('catalogue::layouts.marketplace', [
    'title' => $product->name,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Marketplace', 'url' => route('procurement.marketplace.index')],
        ['name' => $product->name, 'url' => '#']
    ]
])

@section('market-content')
<div class="max-w-[1400px] mx-auto space-y-8 pb-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        {{-- Left Column: Image Gallery --}}
        <div class="lg:col-span-5 relative">
            <div class="sticky top-6 space-y-4">
                <div id="main-image-container" class="aspect-[4/5] rounded-3xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/20 transition-all duration-500 hover:shadow-primary-500/10">
                    @php
                         $firstItem = $product->items->first();
                         $primaryImage = $firstItem ? $firstItem->primaryImage : null;
                    @endphp
                    @if($primaryImage)
                        <img id="main-image" src="{{ $primaryImage->url }}" class="w-full h-full object-cover transition-all duration-700 hover:scale-110" onerror="this.src='{{ asset('assets/img/products/default-product.png') }}'">
                    @else
                        <img id="main-image" src="{{ asset('assets/img/products/default-product.png') }}" class="w-full h-full object-cover opacity-50">
                    @endif
                </div>

                {{-- Back button for mobile --}}
                <a href="{{ route('procurement.marketplace.index') }}" class="lg:hidden flex items-center justify-center gap-2 py-4 text-gray-500 font-bold hover:text-primary-600 transition">
                    <i data-feather="arrow-left" class="w-4 h-4"></i>
                    Back to Marketplace
                </a>
            </div>
        </div>

        {{-- Right Column: product Info & Purchase --}}
        <div class="lg:col-span-7 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 md:p-10 shadow-sm">
                {{-- Header --}}
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-primary-100 dark:border-primary-800">
                            {{ $product->category->name ?? 'General' }}
                        </span>
                        <div class="flex items-center gap-1 px-3 py-1 bg-yellow-50 dark:bg-yellow-900/10 text-yellow-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-yellow-100 dark:border-yellow-800">
                            <i data-feather="star" class="w-3 h-3 fill-current"></i>
                            <span>4.9 High Rated</span>
                        </div>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white leading-tight tracking-tight">
                        {{ $product->name }}
                    </h1>
                    
                    <div class="flex flex-wrap items-center gap-6 text-sm font-bold">
                        <div class="flex items-center gap-2 text-primary-600">
                            <i data-feather="briefcase" class="w-4 h-4 text-gray-400"></i>
                            <span>{{ $product->company->name ?? 'Premium Vendor' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500">
                            <i data-feather="map-pin" class="w-4 h-4 text-gray-300"></i>
                            <span class="font-bold text-xs uppercase tracking-wider">{{ $product->company->address ?? 'Global Shipping' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mt-8 pt-8 border-t border-gray-50 dark:border-gray-700">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Product Overview</h3>
                    <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 leading-relaxed text-base">
                        {{ $product->description }}
                    </div>
                </div>

                {{-- Order Form --}}
                <form action="{{ route('procurement.marketplace.cart.add') }}" method="POST" class="mt-12 space-y-10">
                    @csrf
                    
                    {{-- Hidden SKU ID Input --}}
                    <input type="hidden" name="sku_id" id="selected_sku_id" value="{{ $product->items->first()->id ?? '' }}">

                    {{-- Variant Selection --}}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Select Variant</h3>
                            <span class="text-[10px] text-primary-600 font-bold uppercase tracking-widest">Available Stock</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($product->items as $item)
                                <div class="relative">
                                    <div 
                                        onclick="selectVariant('{{ $item->id }}', this, '{{ $item->stock }}', '{{ $item->primaryImage ? $item->primaryImage->url : asset('assets/img/products/default-product.png') }}')"
                                        class="block relative group border-2 rounded-2xl p-4 cursor-pointer transition-all select-variant-card {{ $loop->first ? 'border-primary-600 bg-primary-50/30 dark:bg-primary-900/10' : 'border-gray-100 dark:border-gray-700 hover:border-primary-200' }}"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-2 h-2 rounded-full {{ $item->stock > 0 ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-red-500' }}"></div>
                                                    <p class="text-xs font-black text-gray-900 dark:text-white tracking-tight uppercase">{{ $item->sku }}</p>
                                                </div>
                                                <div class="space-y-1">
                                                    @foreach($item->attributes as $attr)
                                                        <div class="flex items-center gap-1.5 text-[9px]">
                                                            <span class="font-bold text-gray-400 uppercase tracking-tighter">{{ $attr->attribute_key }}:</span>
                                                            <span class="font-black text-gray-700 dark:text-gray-300">{{ $attr->attribute_value }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                 <p class="text-sm font-black {{ $item->stock > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $item->stock }}</p>
                                                 <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">PCS</p>
                                            </div>
                                        </div>
                                        
                                        {{-- Selected Mark --}}
                                        <div class="absolute top-2 right-2 opacity-0 group-[.border-primary-600]:opacity-100 transition-opacity">
                                            <i data-feather="check-circle" class="w-4 h-4 text-primary-600"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Inputs --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-10 border-t border-gray-100 dark:border-gray-700">
                        <div class="space-y-3">
                            <label for="quantity" class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <i data-feather="hash" class="w-3 h-3"></i> Quantity
                            </label>
                            <div class="relative flex items-center">
                                <button type="button" onclick="adjustQty(-1)" class="absolute left-2 w-10 h-10 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition active:scale-95 shadow-sm">
                                    <i data-feather="minus" class="w-4 h-4"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" 
                                    class="w-full text-center py-4 rounded-2xl border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900 font-black text-xl text-gray-900 dark:text-white shadow-inner focus:ring-0 focus:border-primary-500 transition-all">
                                <button type="button" onclick="adjustQty(1)" class="absolute right-2 w-10 h-10 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition active:scale-95 shadow-sm text-primary-600">
                                    <i data-feather="plus" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label for="delivery_point" class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <i data-feather="truck" class="w-3 h-3"></i> Delivery Point
                            </label>
                            <input type="text" name="delivery_point" id="delivery_point" placeholder="e.g. Warehouse A / Floor 2" 
                                class="w-full px-6 py-4 rounded-2xl border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900 font-bold text-gray-900 dark:text-white shadow-inner focus:ring-0 focus:border-primary-500 transition-all" required>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-primary-600 text-white rounded-2xl py-5 font-black text-base shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-95 flex items-center justify-center gap-3 group">
                            <i data-feather="shopping-cart" class="w-5 h-5"></i>
                            ADD TO PURCHASE CART
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function selectVariant(id, element, stock, imageUrl) {
        // Update Hidden Input
        document.getElementById('selected_sku_id').value = id;

        // Highlight logic
        document.querySelectorAll('.select-variant-card').forEach(el => {
            el.classList.remove('border-primary-600', 'bg-primary-50/30', 'dark:bg-primary-900/10');
            el.classList.add('border-gray-100', 'dark:border-gray-700', 'hover:border-primary-200');
        });
        
        element.classList.add('border-primary-600', 'bg-primary-50/30', 'dark:bg-primary-900/10');
        element.classList.remove('border-gray-100', 'dark:border-gray-700', 'hover:border-primary-200');

        // Update Image
        if(imageUrl) {
            const img = document.getElementById('main-image');
            if(img) {
                img.style.opacity = '0.5';
                setTimeout(() => {
                    img.src = imageUrl;
                    img.style.opacity = '1';
                }, 150);
            }
        }
    }

    function adjustQty(amount) {
        const input = document.getElementById('quantity');
        let val = parseInt(input.value) + amount;
        if (val < 1) val = 1;
        input.value = val;
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Item added to cart',
                html: '<p class="text-sm text-gray-500">{{ session('success') }}</p>',
                showCancelButton: true,
                confirmButtonText: '<div class="flex items-center gap-2"><i data-feather="arrow-right"></i> Go to Cart</div>',
                cancelButtonText: 'Continue Shopping',
                confirmButtonColor: '#05b0a3', // Use your brand primary color
                showClass: {
                    popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('procurement.marketplace.cart') }}";
                }
            });
            
            // Re-replace feather icons in SweetAlert after showing
            setTimeout(() => {
                const swalIcon = document.querySelector('.swal2-confirm i');
                if(swalIcon && typeof feather !== 'undefined') {
                    feather.replace();
                }
            }, 100);
        @endif
    });
</script>
@endsection
