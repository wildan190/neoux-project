{{-- The Map: Progress Stepper --}}
<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 mb-8 shadow-sm">
    @php
        $status = $purchaseOrder->status;
        $escrow = $purchaseOrder->escrow_status;
        
        $steps = [
            ['label' => 'ISSUED', 'icon' => 'send', 'active' => true],
            ['label' => 'ACCEPTED', 'icon' => 'check-circle', 'active' => in_array($status, ['issued', 'confirmed', 'partial_delivery', 'full_delivery', 'completed'])],
            ['label' => 'ESCROW', 'icon' => 'shield', 'active' => in_array($escrow, ['paid', 'released'])],
            ['label' => 'SHIPPING', 'icon' => 'truck', 'active' => $purchaseOrder->deliveryOrders->where('status', 'shipped')->count() > 0],
            ['label' => 'RECEIVED', 'icon' => 'package', 'active' => in_array($status, ['partial_delivery', 'full_delivery', 'completed'])],
            ['label' => 'RELEASED', 'icon' => 'unlock', 'active' => $escrow === 'released'],
        ];
        
        // Map percentage for line
        $activeIndex = 0;
        foreach($steps as $i => $s) if($s['active']) $activeIndex = $i;
        $percentage = ($activeIndex / (count($steps) - 1)) * 100;
    @endphp

    <div class="flex items-center justify-between relative max-w-4xl mx-auto">
        {{-- Background Lines --}}
        <div class="absolute left-0 top-[22px] w-full h-0.5 bg-gray-100 dark:bg-gray-700 -z-0"></div>
        <div class="absolute left-0 top-[22px] h-0.5 bg-primary-600 transition-all duration-1000 -z-0" style="width: {{ $percentage }}%"></div>
        
        @foreach($steps as $index => $step)
            <div class="relative z-10 flex flex-col items-center group">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center transition-all duration-500 border-4 border-white dark:border-gray-800
                    {{ $step['active'] ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'bg-gray-50 dark:bg-gray-700 text-gray-300' }}">
                    <i data-feather="{{ $step['icon'] }}" class="w-4 h-4"></i>
                </div>
                <span class="absolute top-14 text-[9px] font-black tracking-widest transition-colors duration-500 {{ $step['active'] ? 'text-primary-600' : 'text-gray-400' }}">
                    {{ $step['label'] }}
                </span>
            </div>
        @endforeach
    </div>
    
    @php
        $nextAction = '';
        if ($status === 'pending_vendor_acceptance') $nextAction = 'Waiting for Vendor to review and accept this order.';
        elseif (in_array($status, ['issued', 'confirmed']) && $escrow === 'pending') $nextAction = 'Vendor accepted. Buyer needs to pay to Escrow account.';
        elseif (in_array($status, ['issued', 'confirmed']) && $escrow === 'paid') $nextAction = 'Escrow secured. Vendor is ready to ship the items.';
        elseif ($status === 'partial_delivery') $nextAction = 'Some items received. Awaiting remaining shipments.';
        elseif ($status === 'full_delivery' && $escrow === 'paid') $nextAction = 'All items received. Escrow payout is ready for release.';
        elseif ($status === 'completed' || $escrow === 'released') $nextAction = 'Transaction completed successfully.';
    @endphp

    @if($nextAction)
        <div class="mt-20 pt-8 border-t border-gray-50 dark:border-gray-700 flex items-center justify-center gap-4">
            <span class="w-2 h-2 bg-primary-500 rounded-full animate-ping"></span>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ $nextAction }}</p>
        </div>
    @endif
</div>
