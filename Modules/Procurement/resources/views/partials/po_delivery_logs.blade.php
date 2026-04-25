{{-- Delivery Logs --}}
@if($purchaseOrder->deliveryOrders->isNotEmpty())
<div class="space-y-4">
    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest px-4">Shipment History</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($purchaseOrder->deliveryOrders as $do)
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-center gap-4 group hover:shadow-lg hover:shadow-indigo-500/5 transition duration-300">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center text-indigo-600">
                    <i data-feather="package" class="w-6 h-6"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $do->do_number }}</p>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest
                            @if($do->status === 'shipped') bg-blue-50 text-blue-600 @else bg-gray-50 text-gray-500 @endif">
                            {{ $do->status }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        @if($do->status === 'shipped')
                            Shipped {{ $do->shipped_at->diffForHumans() }}
                        @else
                            Created {{ $do->created_at->format('d M') }}
                        @endif
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('procurement.do.print', $do) }}" target="_blank" 
                        class="p-2 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 transition shadow-sm border border-gray-100 dark:border-gray-600"
                        title="Print DO">
                        <i data-feather="printer" class="w-4 h-4"></i>
                    </a>
                    
                    @if($do->status === 'pending' && $isVendor)
                        <button onclick="shipOrder('{{ $do->id }}')" class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-lg shadow-indigo-500/20" title="Ship Order">
                            <i data-feather="send" class="w-4 h-4"></i>
                        </button>
                        <form id="ship-form-{{ $do->id }}" action="{{ route('procurement.do.ship', $do) }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="tracking_number" id="tracking-input-{{ $do->id }}">
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
