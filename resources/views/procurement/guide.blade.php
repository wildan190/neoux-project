@extends('layouts.app', [
    'title' => 'Procurement Guide',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.po.index')],
        ['name' => 'Process Guide', 'url' => '#'],
    ]
])

@section('content')
<div class="max-w-5xl mx-auto space-y-16 pb-24" x-data="{ role: 'buyer' }">
    
    {{-- Hero Section --}}
    <div class="text-center space-y-6 pt-12">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-bold uppercase tracking-wider">
            <i data-feather="book-open" class="w-4 h-4"></i>
            User Manual
        </div>
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tighter leading-tight">
            Master the <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Procurement Flow</span>
        </h1>
        <p class="text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
            Understand the complete lifecycle of a transaction in NeoUX. <br class="hidden md:block"> Select your role to see your journey.
        </p>

        {{-- Premium Toggle --}}
        <div class="inline-flex bg-white dark:bg-gray-800 p-1.5 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 relative mt-8">
            {{-- Sliding Background (Visual trick handled by simple conditional classes for now) --}}
            <button @click="role = 'buyer'" 
                class="relative px-8 py-3 rounded-xl text-sm font-bold transition-all duration-300 flex items-center gap-2 z-10"
                :class="role === 'buyer' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white'">
                <i data-feather="shopping-bag" class="w-4 h-4"></i>
                Buyer Journey
            </button>
            <button @click="role = 'vendor'" 
                class="relative px-8 py-3 rounded-xl text-sm font-bold transition-all duration-300 flex items-center gap-2 z-10"
                :class="role === 'vendor' ? 'bg-indigo-600 text-white shadow-lg' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white'">
                <i data-feather="truck" class="w-4 h-4"></i>
                Vendor Journey
            </button>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="relative">
        {{-- Vertical Line --}}
        <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 transform -translate-x-1/2"></div>

        {{-- Steps Container --}}
        <div class="space-y-24">
            
            {{-- BUYER STEPS --}}
            <div x-show="role === 'buyer'" class="space-y-24" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0">
                
                {{-- Step 1: Selection & Checkout --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-2 md:order-1 flex-1 md:text-right">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Select & Checkout</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            Browse the <strong>Catalogue</strong>, add items to your <strong>Cart</strong>, and proceed to <strong>Checkout</strong>. This creates a requisition request.
                        </p>
                        <div class="flex flex-wrap justify-end gap-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider rounded-lg">Catalogue</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider rounded-lg">Manual PR</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 italic">
                            *Can also create Manual PR directly without Catalogue.
                        </p>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-blue-500 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">1</span>
                    </div>
                    <div class="order-3 flex-1 pl-12 md:pl-0">
                         <div class="w-full h-48 bg-blue-50 dark:bg-blue-900/20 rounded-3xl flex items-center justify-center border border-blue-100 dark:border-blue-800/30">
                            <i data-feather="shopping-cart" class="w-16 h-16 text-blue-500"></i>
                         </div>
                    </div>
                </div>

                {{-- Step 2: Approval --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-3 md:order-1 flex-1 md:text-right pl-12 md:pl-0">
                         <div class="w-full h-48 bg-purple-50 dark:bg-purple-900/20 rounded-3xl flex items-center justify-center border border-purple-100 dark:border-purple-800/30">
                            <i data-feather="user-check" class="w-16 h-16 text-purple-500"></i>
                         </div>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-purple-500 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">2</span>
                    </div>
                    <div class="order-2 md:order-3 flex-1 text-left">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Internal Approval</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            The request is sent to management for approval. Once approved, it becomes an official <strong>Purchase Requisition (PR)</strong> ready for the market.
                        </p>
                        <div class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white">
                            <i data-feather="check-square" class="w-4 h-4 text-purple-500"></i>
                            Manager Approval
                        </div>
                    </div>
                </div>

                {{-- Step 3: Compare & Negotiate --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-2 md:order-1 flex-1 md:text-right">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Compare & Negotiate</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            Receive offers from vendors. <strong>Compare</strong> prices and specs. If needed, start a <strong>Negotiation</strong> to get better terms.
                        </p>
                        <div class="flex flex-wrap justify-end gap-2">
                             <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-wider rounded-lg">Compare Offers</span>
                             <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-wider rounded-lg">Negotiate</span>
                        </div>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-amber-500 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">3</span>
                    </div>
                    <div class="order-3 flex-1 pl-12 md:pl-0">
                         <div class="w-full h-48 bg-amber-50 dark:bg-amber-900/20 rounded-3xl flex items-center justify-center border border-amber-100 dark:border-amber-800/30">
                            <i data-feather="git-pull-request" class="w-16 h-16 text-amber-500"></i>
                         </div>
                    </div>
                </div>

                {{-- Step 4: PO & Receive --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-3 md:order-1 flex-1 md:text-right pl-12 md:pl-0">
                         <div class="w-full h-48 bg-green-50 dark:bg-green-900/20 rounded-3xl flex items-center justify-center border border-green-100 dark:border-green-800/30">
                            <i data-feather="package" class="w-16 h-16 text-green-500"></i>
                         </div>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-green-500 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">4</span>
                    </div>
                    <div class="order-2 md:order-3 flex-1 text-left">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">PO & Fulfillment</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            Select a winner to <strong>Generate PO</strong>. Wait for vendor confirmation. when items arrive, create a <strong>Goods Receipt (GR)</strong>.
                        </p>
                        <div class="flex flex-wrap gap-2">
                             <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-lg">Generate PO</span>
                             <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-lg">Receive Goods</span>
                        </div>
                    </div>
                </div>

                 {{-- Step 5: Invoice --}}
                 <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-2 md:order-1 flex-1 md:text-right">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Receive Invoice</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            Finally, receive the <strong>Invoice</strong> from the vendor for payment processing.
                        </p>
                        <div class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white">
                            <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                            Process Payment
                        </div>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-gray-900 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">5</span>
                    </div>
                    <div class="order-3 flex-1 pl-12 md:pl-0">
                         <div class="w-full h-48 bg-gray-50 dark:bg-gray-900/20 rounded-3xl flex items-center justify-center border border-gray-100 dark:border-gray-800/30">
                            <i data-feather="file-text" class="w-16 h-16 text-gray-900 dark:text-gray-100"></i>
                         </div>
                    </div>
                </div>
            </div>

            {{-- VENDOR STEPS --}}
            <div x-show="role === 'vendor'" class="space-y-24" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                
                {{-- Step 1: Tender Publish --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-2 md:order-1 flex-1 md:text-right">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Get New Tender</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            Receive notifications for new <strong>Tender / Requests</strong> published by buyers that match your category.
                        </p>
                        <div class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white">
                            <i data-feather="bell" class="w-4 h-4 text-indigo-500"></i>
                            Real-time Notification
                        </div>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-indigo-500 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">1</span>
                    </div>
                    <div class="order-3 flex-1 pl-12 md:pl-0">
                         <div class="w-full h-48 bg-indigo-50 dark:bg-indigo-900/20 rounded-3xl flex items-center justify-center border border-indigo-100 dark:border-indigo-800/30">
                            <i data-feather="rss" class="w-16 h-16 text-indigo-500"></i>
                         </div>
                    </div>
                </div>

                {{-- Step 2: Submit Offer --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-3 md:order-1 flex-1 md:text-right pl-12 md:pl-0">
                         <div class="w-full h-48 bg-emerald-50 dark:bg-emerald-900/20 rounded-3xl flex items-center justify-center border border-emerald-100 dark:border-emerald-800/30">
                            <i data-feather="send" class="w-16 h-16 text-emerald-500"></i>
                         </div>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-emerald-500 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">2</span>
                    </div>
                    <div class="order-2 md:order-3 flex-1 text-left">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Submit Offer</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            Fill out the form data with your best price and stock availability. <strong>Submit</strong> your offer for the buyer to review.
                        </p>
                        <a href="{{ route('procurement.marketplace.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-bold rounded-xl hover:bg-gray-200 transition">
                            View Marketplace
                        </a>
                    </div>
                </div>

                {{-- Step 3: Negotiate & Deal --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-2 md:order-1 flex-1 md:text-right">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Negotiation & Deal</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            If the buyer requests a <strong>Negotiation</strong>, respond with a new price. Reach a <strong>Deal</strong> to win the bid.
                        </p>
                        <div class="flex flex-wrap justify-end gap-2">
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-wider rounded-lg">Negotiate</span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-lg">Deal</span>
                        </div>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-amber-500 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">3</span>
                    </div>
                    <div class="order-3 flex-1 pl-12 md:pl-0">
                         <div class="w-full h-48 bg-amber-50 dark:bg-amber-900/20 rounded-3xl flex items-center justify-center border border-amber-100 dark:border-amber-800/30">
                            <i data-feather="message-circle" class="w-16 h-16 text-amber-500"></i>
                         </div>
                    </div>
                </div>

                {{-- Step 4: PO Confirm & Ship --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-3 md:order-1 flex-1 md:text-right pl-12 md:pl-0">
                         <div class="w-full h-48 bg-blue-50 dark:bg-blue-900/20 rounded-3xl flex items-center justify-center border border-blue-100 dark:border-blue-800/30">
                            <i data-feather="truck" class="w-16 h-16 text-blue-500"></i>
                         </div>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-blue-500 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">4</span>
                    </div>
                    <div class="order-2 md:order-3 flex-1 text-left">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">PO & Shipping</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            If you win, <strong>Confirm PO</strong> from the buyer. Then, <strong>Arrange Shipping</strong> and input the tracking number.
                        </p>
                        <div class="flex flex-wrap gap-2">
                             <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-lg">Confirm PO</span>
                             <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider rounded-lg">Arrange Shipping</span>
                        </div>
                    </div>
                </div>

                {{-- Step 5: Invoice --}}
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8 group">
                    <div class="order-2 md:order-1 flex-1 md:text-right">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Generate Invoice</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed">
                            Once items are confirmed as <strong>Received</strong> by the buyer, you can <strong>Generate Invoice</strong> to get paid.
                        </p>
                        <a href="{{ route('procurement.invoices.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-bold rounded-xl hover:bg-gray-200 transition">
                             Create Invoice
                        </a>
                    </div>
                    <div class="order-1 md:order-2 w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border-4 border-gray-900 flex items-center justify-center shadow-2xl relative z-10 transform transition group-hover:scale-110 duration-300">
                        <span class="font-black text-2xl text-gray-900 dark:text-white">5</span>
                    </div>
                    <div class="order-3 flex-1 pl-12 md:pl-0">
                         <div class="w-full h-48 bg-gray-50 dark:bg-gray-900/20 rounded-3xl flex items-center justify-center border border-gray-100 dark:border-gray-800/30">
                            <i data-feather="dollar-sign" class="w-16 h-16 text-gray-900 dark:text-gray-100"></i>
                         </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Footer CTA --}}
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-12 text-center text-white relative overflow-hidden mt-24">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-black mb-4">Ready to start?</h2>
            <p class="text-gray-400 mb-8 max-w-xl mx-auto">Jump right in and optimize your procurement process today.</p>
            <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white text-gray-900 text-sm font-black uppercase tracking-widest rounded-xl hover:bg-blue-50 transition transform hover:scale-105 shadow-2xl">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection
