@extends('layouts.app', [
    'title' => 'Shopping Cart',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Marketplace', 'url' => route('procurement.marketplace.index')],
        ['name' => 'Cart', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Shopping Cart</h1>

    @if(count($cart) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($cart as $id => $details)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            @if(!empty($details['image']))
                                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $details['image']) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                    <i data-feather="image" class="w-4 h-4 text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $details['name'] }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <span class="font-medium">Delivery:</span> {{ $details['delivery_point'] }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                    {{ $details['quantity'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <form action="{{ route('procurement.marketplace.cart.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="sku_id" value="{{ $id }}">
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Grand Total removed --}}
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('procurement.marketplace.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium flex items-center gap-2">
                <i data-feather="arrow-left" class="w-4 h-4"></i> Continue Shopping
            </a>
            
            <form action="{{ route('procurement.marketplace.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all flex items-center gap-2">
                    Checkout Purchase Request <i data-feather="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 mb-6">
                <i data-feather="shopping-cart" class="w-10 h-10 text-gray-400"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Your cart is empty</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('procurement.marketplace.index') }}" class="inline-block px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">
                Start Shopping
            </a>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection
