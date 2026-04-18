@extends('layouts.app', [
    'title' => 'Shopping Cart',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Marketplace', 'url' => route('procurement.marketplace.index')],
        ['name' => 'Cart', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-5xl mx-auto pb-20">
    <div class="flex flex-col md:flex-row items-center justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Purchase Cart</h1>
            <p class="text-sm text-gray-500 font-medium">Review your items before submitting a purchase requisition.</p>
        </div>
        <a href="{{ route('procurement.marketplace.index') }}" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-sm font-bold text-gray-600 dark:text-gray-300 hover:text-primary-600 transition shadow-sm active:scale-95">
            <i data-feather="arrow-left" class="w-4 h-4 text-primary-600"></i>
            Continue Shopping
        </a>
    </div>

    @if(count($cart) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Desktop View --}}
                <div class="hidden md:block bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                    <table class="w-full">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Product Details</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Qty</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($cart as $id => $details)
                                <tr class="group hover:bg-gray-50/30 dark:hover:bg-gray-700/10 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-6">
                                            <div class="h-16 w-16 flex-shrink-0 bg-gray-50 dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 shadow-sm">
                                                @if(!empty($details['image']))
                                                    <img class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500" src="{{ asset('storage/' . $details['image']) }}" alt="">
                                                @else
                                                    <img class="h-full w-full object-cover opacity-50" src="{{ asset('assets/img/products/default-product.png') }}" alt="">
                                                @endif
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-base font-bold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">{{ $details['name'] }}</div>
                                                <div class="text-[10px] font-black uppercase tracking-widest text-primary-500 flex items-center gap-1">
                                                    <i data-feather="map-pin" class="w-3 h-3"></i>
                                                    {{ $details['delivery_point'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="inline-flex items-center justify-center p-3 w-12 h-12 bg-gray-100 dark:bg-gray-900 rounded-xl font-black text-gray-900 dark:text-white text-base">
                                            {{ $details['quantity'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <form action="{{ route('procurement.marketplace.cart.remove') }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="sku_id" value="{{ $id }}">
                                            <button type="submit" class="w-10 h-10 bg-red-50 dark:bg-red-900/20 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-sm active:scale-95">
                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View --}}
                <div class="md:hidden space-y-4">
                    @foreach($cart as $id => $details)
                        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                            <div class="flex gap-4">
                                <div class="h-20 w-20 flex-shrink-0 bg-gray-50 dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                                    @if(!empty($details['image']))
                                        <img class="h-full w-full object-cover" src="{{ asset('storage/' . $details['image']) }}" alt="">
                                    @else
                                        <img class="h-full w-full object-cover opacity-50" src="{{ asset('assets/img/products/default-product.png') }}" alt="">
                                    @endif
                                </div>
                                <div class="flex-1 space-y-1">
                                    <div class="text-base font-bold text-gray-900 dark:text-white">{{ $details['name'] }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-primary-500">{{ $details['delivery_point'] }}</div>
                                    <div class="pt-2 flex items-center justify-between">
                                        <p class="text-sm font-black text-gray-500">Qty: {{ $details['quantity'] }}</p>
                                        <form action="{{ route('procurement.marketplace.cart.remove') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="sku_id" value="{{ $id }}">
                                            <button type="submit" class="text-xs font-black text-red-500 uppercase tracking-widest">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Checkout CTA Section --}}
            <div class="lg:col-span-1">
                <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-gray-900/30 sticky top-6">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <i data-feather="shopping-bag" class="w-32 h-32 -mr-8 -mt-8"></i>
                    </div>
                    
                    <div class="relative z-10 space-y-10">
                        <div>
                            <h3 class="text-xl font-black mb-2 tracking-tight">Summary</h3>
                            <div class="h-[1px] w-full bg-gradient-to-r from-primary-500 to-transparent opacity-30 mt-4"></div>
                        </div>

                        <div class="space-y-6">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-400 font-bold uppercase tracking-widest">Total Items</span>
                                <span class="text-xl font-black">{{ count($cart) }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-6 border-b border-white/10">
                                <span class="text-sm text-gray-400 font-bold uppercase tracking-widest">Total Quantity</span>
                                <span class="text-xl font-black">{{ array_sum(array_column($cart, 'quantity')) }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="title" class="text-xs text-gray-400 font-bold uppercase tracking-widest">Request Title</label>
                                <input type="text" name="title" id="title" form="checkout-form" placeholder="e.g. Project X Supplies" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            </div>

                            <p class="text-xs text-gray-400 font-medium italic">All selected items will be converted into a single Purchase Requisition (Tender Request).</p>
                            
                            <form id="checkout-form" action="{{ route('procurement.marketplace.checkout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white rounded-[1.25rem] py-5 font-black text-base shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 group">
                                    PROCESS TO CHECKOUT
                                    <i data-feather="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-[4rem] p-20 text-center border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
             <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-primary-600 via-primary-300 to-primary-600"></div>
            
            <div class="w-32 h-32 mx-auto mb-10 bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] flex items-center justify-center relative shadow-inner">
                <i data-feather="shopping-cart" class="w-12 h-12 text-gray-300"></i>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-xs ring-4 ring-white dark:ring-gray-800 animate-pulse">0</div>
            </div>
            
            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-4 tracking-tight">Your cart is empty</h2>
            <p class="text-gray-500 dark:text-gray-400 text-lg mb-10 max-w-md mx-auto">Looks like you haven't added any items to your cart yet. Time to discover some great products!</p>
            
            <a href="{{ route('procurement.marketplace.index') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white font-black rounded-2xl hover:bg-primary-600 hover:text-white transition-all shadow-xl active:scale-95 group">
                <i data-feather="search" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
                START EXPLORING
            </a>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
