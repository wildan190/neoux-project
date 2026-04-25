<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-12 transition-all duration-500">
    <div class="grid md:grid-cols-2 gap-0">
        
        {{-- PRODUCT IMAGES --}}
        <div class="bg-gray-50 dark:bg-gray-900/50 p-6 md:p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-800">
            <div class="w-full max-w-sm aspect-square rounded-2xl overflow-hidden bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/30 dark:shadow-black/50 border border-gray-100 dark:border-gray-800 mb-6 relative group">
                @if($product->primaryImage)
                     <img src="{{ $product->primaryImage->url }}" alt="{{ $product->product?->name }}" class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-700" id="main-product-image">
                @else
                     <div class="w-full h-full flex items-center justify-center text-gray-200 dark:text-gray-700">
                         <i data-feather="image" class="w-16 h-16"></i>
                     </div>
                @endif
                <div class="absolute top-4 right-4">
                    <div class="bg-primary-600 text-white p-2 rounded-xl shadow-lg">
                        <i data-feather="zoom-in" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            @if($product->images->count() > 1)
                <div class="flex gap-3 overflow-x-auto w-full max-w-sm px-1 pb-2 hide-scrollbar">
                    @foreach($product->images as $img)
                        <button onclick="document.getElementById('main-product-image').src='{{ $img->url }}'" class="w-16 h-16 flex-shrink-0 rounded-xl border-2 {{ $img->is_primary ? 'border-primary-500 shadow-lg shadow-primary-500/10' : 'border-gray-100 dark:border-gray-800' }} overflow-hidden bg-white dark:bg-gray-800 hover:border-primary-400 focus:outline-none transition-all">
                            <img src="{{ $img->url }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- PRODUCT DETAILS --}}
        <div class="p-6 md:p-10 flex flex-col">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-[9px] font-bold uppercase tracking-widest rounded-lg border border-primary-100 dark:border-primary-800/50">
                    {{ $product->product?->category?->name ?? 'Product' }}
                </span>
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                    SKU: {{ $product->sku }}
                </span>
            </div>

            <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4 leading-tight tracking-tight">
                {{ $product->product?->name ?? $product->name }}
            </h1>
            
            <div class="prose prose-sm dark:prose-invert max-w-none mb-8">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3">Product Description</p>
                <div class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm">
                    {!! nl2br(e($product->product?->description ?? $product->description ?? 'Tidak ada deskripsi tersedia.')) !!}
                </div>
            </div>

            @php
                // Handle both CatalogueProduct and CatalogueItem models
                $isItem = $product instanceof \Modules\Catalogue\Models\CatalogueItem;
                $items = $isItem ? collect([$product]) : ($product->items ?? collect());
                $specItem = $isItem ? $product : $items->first();
                $specs = $specItem ? $specItem->attributes : collect();
            @endphp

            @if($specs->isNotEmpty())
                <div class="mb-8">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3">Specifications</p>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($specs as $attr)
                            <div class="bg-gray-50 dark:bg-gray-900/30 p-3 rounded-xl border border-gray-100 dark:border-gray-800 group hover:border-primary-500/30 transition-colors">
                                <p class="text-[8px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5 group-hover:text-primary-600">{{ $attr->attribute_key }}</p>
                                <p class="font-bold text-gray-900 dark:text-white text-xs tracking-tight">{{ $attr->attribute_value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Order Form --}}
            <form action="{{ route('procurement.marketplace.cart.add') }}" method="POST" class="mt-8 space-y-8">
                @csrf
                
                {{-- Defensive SKU Resolution --}}
                @php
                    $items = $product->items ?? null;
                    $firstItem = ($items && $items->isNotEmpty()) ? $items->first() : null;
                    $initialSkuId = $firstItem ? $firstItem->id : $product->id;
                @endphp

                {{-- Hidden SKU ID Input --}}
                <input type="hidden" name="sku_id" id="selected_sku_id" value="{{ $initialSkuId }}">

                {{-- Variant Selection --}}
                @if($items && $items->isNotEmpty())
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-widest">Select Variant</h3>
                        <span class="text-[10px] text-primary-600 font-bold uppercase tracking-widest">Stock Available</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($items as $item)
                            <div class="relative">
                                <div 
                                    onclick="selectVariant('{{ $item->id }}', this, '{{ $item->stock }}', '{{ $item->primaryImage ? $item->primaryImage->url : asset('assets/img/products/default-product.png') }}')"
                                    class="block relative group border-2 rounded-xl p-4 cursor-pointer hover:border-primary-500 transition-all select-variant-card {{ $loop->first ? 'border-primary-600 bg-primary-50/50 dark:bg-primary-900/20' : 'border-gray-100 dark:border-gray-700 bg-gray-50/30' }}"
                                >
                                    <div class="flex justify-between items-start">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full {{ $item->stock > 0 ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-red-500' }}"></div>
                                                <p class="font-bold text-gray-900 dark:text-white tracking-tight text-xs">{{ $item->sku }}</p>
                                            </div>
                                            <div class="space-y-0.5">
                                                @foreach($item->attributes as $attr)
                                                    <div class="flex items-center gap-1.5 text-[9px]">
                                                        <span class="font-bold text-gray-400 uppercase tracking-tighter">{{ $attr->attribute_key ?? $attr->name }}:</span>
                                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $attr->attribute_value ?? $attr->value }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="text-right">
                                             <p class="text-[11px] font-bold {{ $item->stock > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $item->stock }}</p>
                                             <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Units Left</p>
                                        </div>
                                    </div>
                                    
                                    {{-- Selected Mark --}}
                                    <div class="absolute top-2 right-2 opacity-0 group-[.border-primary-600]:opacity-100 transition-opacity">
                                        <div class="w-4 h-4 bg-primary-600 rounded-full flex items-center justify-center text-white">
                                            <i data-feather="check" class="w-2.5 h-2.5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Inputs --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-8 border-t border-gray-100 dark:border-gray-800">
                    <div class="space-y-3">
                        <label for="quantity" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <i data-feather="hash" class="w-3 h-3"></i> Quantity
                        </label>
                        <div class="relative flex items-center">
                            <button type="button" onclick="adjustQty(-1)" class="absolute left-1.5 w-8 h-8 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition active:scale-95 shadow-sm">
                                <i data-feather="minus" class="w-3.5 h-3.5"></i>
                            </button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" 
                                class="w-full text-center py-3 rounded-xl border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900 font-bold text-lg text-gray-900 dark:text-white shadow-inner focus:ring-0 focus:border-primary-500 transition-all">
                            <button type="button" onclick="adjustQty(1)" class="absolute right-1.5 w-8 h-8 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition active:scale-95 shadow-sm text-primary-600">
                                <i data-feather="plus" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label for="delivery_point" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <i data-feather="truck" class="w-3 h-3"></i> Delivery Point
                        </label>
                        <select name="delivery_point" id="delivery_point" 
                            class="w-full px-5 py-3 rounded-xl border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900 font-bold text-sm text-gray-900 dark:text-white shadow-inner focus:ring-0 focus:border-primary-500 transition-all appearance-none" required>
                            <option value="">-- Select Storage Point --</option>
                            @if(isset($locations))
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->address }}">{{ $loc->address }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl py-3.5 font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-primary-500/30 hover:-translate-y-0.5 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i data-feather="shopping-cart" class="w-4 h-4"></i>
                        Add to Cart
                    </button>
                    <a href="{{ route('procurement.pr.create', ['catalogue_id' => $product->id]) }}" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white px-6 py-3.5 rounded-xl font-bold text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        Direct PR
                    </a>
                </div>
            </form>
            
            <script>
                function selectVariant(id, element, stock, imageUrl) {
                    // Update Hidden Input
                    document.getElementById('selected_sku_id').value = id;

                    // Highlight logic
                    document.querySelectorAll('.select-variant-card').forEach(el => {
                        el.classList.remove('border-primary-600', 'bg-primary-50', 'dark:bg-primary-900/10');
                        el.classList.add('border-gray-100', 'dark:border-gray-700', 'bg-gray-50/30');
                    });

                    element.classList.add('border-primary-600', 'bg-primary-50', 'dark:bg-primary-900/10');
                    element.classList.remove('border-gray-100', 'dark:border-gray-700', 'bg-gray-50/30');

                    if(imageUrl) {
                        const img = document.getElementById('main-product-image');
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
            </script>
        </div>
    </div>
</div>

{{-- RELATED PRODUCTS --}}
@if($relatedProducts && $relatedProducts->count() > 0)
    <div class="mb-20">
        <div class="flex justify-between items-center mb-10">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight italic">Recommended <span class="text-primary-600">Choices</span></h2>
            <a href="{{ route('market.index') }}" class="text-[10px] font-black text-gray-400 hover:text-primary-600 uppercase tracking-widest flex items-center gap-2 transition-colors">
                View All <i data-feather="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            @foreach($relatedProducts as $related)
                <a href="{{ route('market.show', $related->id) }}" class="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden flex flex-col h-full">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-50 dark:bg-gray-900/50 p-6 flex items-center justify-center">
                        @if($related->primaryImage)
                            <img src="{{ $related->primaryImage->url }}" alt="{{ $related->product?->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700">
                        @else
                            <i data-feather="image" class="w-10 h-10 text-gray-200 dark:text-gray-700"></i>
                        @endif
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-900 dark:text-white text-sm mb-2 line-clamp-2 leading-tight group-hover:text-primary-600 transition-colors">
                            {{ $related->product?->name ?? $related->name ?? 'Untitled' }}
                        </h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
