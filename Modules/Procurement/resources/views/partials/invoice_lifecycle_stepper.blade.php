{{-- The Map: Invoice Lifecycle --}}
<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 mb-8 shadow-sm">
    @php
        $status = $invoice->status;
        
        $steps = [
            ['label' => 'SUBMITTED', 'icon' => 'file-text', 'active' => true],
            ['label' => 'MATCHED', 'icon' => 'check-square', 'active' => in_array($status, ['matched', 'vendor_approved', 'purchasing_approved', 'paid'])],
            ['label' => 'APPROVED', 'icon' => 'shield', 'active' => in_array($status, ['vendor_approved', 'purchasing_approved', 'paid'])],
            ['label' => 'DISBURSED', 'icon' => 'credit-card', 'active' => $status === 'paid'],
        ];
        
        $activeIndex = 0;
        foreach($steps as $i => $s) if($s['active']) $activeIndex = $i;
        $percentage = ($activeIndex / (count($steps) - 1)) * 100;
    @endphp

    <div class="flex items-center justify-between relative max-w-2xl mx-auto">
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
</div>
