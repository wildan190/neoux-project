@extends('layouts.app', [
    'title' => $product->name,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Marketplace', 'url' => route('procurement.marketplace.index')],
        ['name' => $product->name, 'url' => '#']
    ]
])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- Image Gallery --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-4 sticky top-6">
            <div id="main-image-container" class="aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 mb-4">
                @php
                     $firstItem = $product->items->first();
                     $primaryImage = $firstItem ? $firstItem->primaryImage : null;
                @endphp
                @if($primaryImage)
                    <img id="main-image" src="{{ $primaryImage->url }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('assets/img/products/default-product.png') }}'">
                @else
                    <img id="main-image" src="{{ asset('assets/img/products/default-product.png') }}" class="w-full h-full object-cover opacity-50">
                @endif
            </div>
            {{-- Thumbnails could go here --}}
        </div>
    </div>

    {{-- Product Info & Purchase --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-8">
            <div class="mb-4">
                <span class="text-sm font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wide">{{ $product->category->name ?? 'General' }}</span>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $product->name }}</h1>
                <div class="flex items-center gap-2 mt-2">
                     <span class="text-sm text-gray-500">{{ $product->brand }}</span>
                     {{-- Rating stars could go here --}}
                </div>
            </div>

            <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 mb-8">
                {{ $product->description }}
            </div>

            <form action="{{ route('procurement.marketplace.cart.add') }}" method="POST">
                @csrf
                <div class="border-t border-b border-gray-100 dark:border-gray-700 py-6 my-6 space-y-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Select Variant</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($product->items as $item)
                                <label class="relative border rounded-xl p-4 cursor-pointer hover:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500 transition-all select-variant-label {{ $loop->first ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input type="radio" name="sku_id" value="{{ $item->id }}" class="sr-only" {{ $loop->first ? 'checked' : '' }} 
                                        onchange="selectVariant(this)"
                                        data-price="Rp {{ number_format($item->price, 0, ',', '.') }}"
                                        data-stock="{{ $item->stock }}"
                                        data-image="{{ $item->primaryImage ? $item->primaryImage->url : asset('assets/img/products/default-product.png') }}"
                                    >
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">{{ $item->sku }}</p>
                                            <div class="text-xs text-gray-500 space-y-1">
                                                @foreach($item->attributes as $attr)
                                                    <div><span class="font-medium">{{ $attr->attribute_key }}:</span> {{ $attr->attribute_value }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="text-right">
                                             <p class="font-bold text-primary-600 dark:text-primary-400">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                             <p class="text-xs {{ $item->stock > 0 ? 'text-green-600' : 'text-red-500' }}">Stock: {{ $item->stock }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="w-32">
                            <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="flex-1">
                             <div class="text-right">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Estimate</label>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="total-price">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-primary-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all flex justify-center items-center gap-2">
                        <i data-feather="shopping-cart" class="w-5 h-5"></i>
                        Add to Cart
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function selectVariant(input) {
        // Highlight logic
        document.querySelectorAll('.select-variant-label').forEach(el => {
            el.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/10');
            el.classList.add('border-gray-200', 'dark:border-gray-700');
        });
        input.parentElement.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/10');
        input.parentElement.classList.remove('border-gray-200', 'dark:border-gray-700');

        // Update Image
        const imageUrl = input.dataset.image;
        if(imageUrl) {
            const img = document.getElementById('main-image');
            if(img) img.src = imageUrl;
        }

        updateTotal();
    }

    function updateTotal() {
        const checked = document.querySelector('input[name="sku_id"]:checked');
        if(!checked) return;

        // Simplify Parsing (remove Rp and dots)
        const priceStr = checked.dataset.price.replace(/[^0-9]/g, '');
        const price = parseInt(priceStr);
        const qty = parseInt(document.getElementById('quantity').value) || 1;
        
        const total = price * qty;
        document.getElementById('total-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.getElementById('quantity').addEventListener('input', updateTotal);

    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        updateTotal();

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endsection
