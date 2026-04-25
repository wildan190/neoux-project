@extends('layouts.app', [
    'title' => 'Inventory Scanner',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'WMS', 'url' => route('warehouse.index')],
        ['name' => 'Scanner', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    {{-- Terminal Header --}}
    <div class="bg-gray-900 rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i data-feather="maximize" class="w-24 h-24"></i>
        </div>
        <div class="relative z-10 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="ml-2 text-[10px] font-black text-gray-500 uppercase tracking-widest">WMS // INVENTORY_SCANNER_v2.0</span>
            </div>
            <h1 class="text-3xl font-black tracking-tighter">PHYSICAL <span class="text-primary-500">ASSET SCANNER</span></h1>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-[0.2em]">Capture & Validate System Stock via QR-Code</p>
        </div>
    </div>

    {{-- Main Interface --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Camera/Scanner Unit --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm relative aspect-square overflow-hidden group">
                {{-- Scanner Frame Overlay --}}
                <div class="absolute inset-0 z-10 flex items-center justify-center p-12 pointer-events-none">
                    <div class="w-full h-full border-2 border-white/20 rounded-3xl relative">
                        <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-primary-500 -mt-1 -ml-1 rounded-tl-lg"></div>
                        <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-primary-500 -mt-1 -mr-1 rounded-tr-lg"></div>
                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-primary-500 -mb-1 -ml-1 rounded-bl-lg"></div>
                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-primary-500 -mb-1 -mr-1 rounded-br-lg"></div>
                        
                        {{-- Scanning Line Animation --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-primary-500 to-transparent shadow-[0_0_15px_rgba(59,130,246,0.5)] animate-scan-line"></div>
                    </div>
                </div>

                {{-- Camera Feed Simulator/Video --}}
                <div id="scanner-container" class="w-full h-full bg-gray-100 dark:bg-gray-900 rounded-2xl flex flex-col items-center justify-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-primary-600/10 flex items-center justify-center text-primary-600 animate-pulse">
                        <i data-feather="camera" class="w-8 h-8"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Waiting for hardware initialization...</p>
                    <button onclick="startScanner()" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl text-[10px] font-black tracking-widest uppercase hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/20 active:scale-95">
                        INITIALIZE CAMERA
                    </button>
                    <div id="reader" style="width: 100%; display: none;"></div>
                </div>
            </div>

            <div class="bg-primary-50 dark:bg-primary-900/10 rounded-2xl p-6 border border-primary-100 dark:border-primary-900/20 text-primary-600">
                <div class="flex items-start gap-4">
                    <i data-feather="info" class="w-5 h-5 mt-0.5"></i>
                    <div class="space-y-1">
                        <h3 class="text-xs font-black uppercase tracking-widest">Direct Input Fallback</h3>
                        <p class="text-[10px] font-medium leading-relaxed opacity-80">If scanning fails, manually enter the SKU below to override system validation.</p>
                        <div class="pt-3 flex gap-2">
                            <input type="text" id="manual-sku" placeholder="ENTER SKU CODE" class="flex-1 bg-white border border-primary-200 rounded-lg px-3 py-2 text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-primary-500 outline-none">
                            <button onclick="lookupSku()" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-xs font-black uppercase tracking-widest hover:bg-primary-700 transition-all">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Results & Actions Unit --}}
        <div class="space-y-8">
            {{-- Target Warehouse Selector --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Target Warehouse</h3>
                <select id="warehouse-id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl px-4 py-3 text-xs font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Validation Result (Hidden by default) --}}
            <div id="result-container" class="hidden animate-in fade-in slide-in-from-bottom-4 duration-300">
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-xl">
                    <div class="bg-green-500 px-6 py-3 flex items-center justify-between">
                        <span class="text-[10px] font-black text-white uppercase tracking-widest">Asset Authenticated</span>
                        <div class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center text-white">
                            <i data-feather="check" class="w-3.5 h-3.5"></i>
                        </div>
                    </div>
                    <div class="p-8 space-y-8">
                        <div class="flex items-start gap-6">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center text-primary-600 border border-gray-100 dark:border-gray-800">
                                <i data-feather="package" class="w-10 h-10"></i>
                            </div>
                            <div class="space-y-1">
                                <h4 id="item-name" class="text-xl font-black text-gray-900 dark:text-white tracking-tight uppercase">Product Name</h4>
                                <p id="item-sku" class="text-xs font-bold text-primary-600 uppercase tracking-widest">SKU: PROD-123456</p>
                                <p id="item-warehouse" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Warehouse: Main Hub</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Current Stock</p>
                                <p id="item-stock" class="text-xl font-black text-gray-900 dark:text-white">0</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Base Price</p>
                                <p id="item-price" class="text-xl font-black text-gray-900 dark:text-white">0</p>
                            </div>
                        </div>

                        {{-- Adjustment Controls --}}
                        <div class="space-y-4 pt-4">
                            <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Execute Adjustment</h5>
                            <div class="flex items-center gap-3">
                                <input type="number" id="adjust-qty" value="1" min="1" class="w-24 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl px-4 py-3 text-center text-lg font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                                <button onclick="adjust('in')" class="flex-1 py-3 bg-green-600 text-white rounded-xl text-xs font-black tracking-widest uppercase hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 active:scale-95">STOCKED IN (+)</button>
                                <button onclick="adjust('out')" class="flex-1 py-3 bg-red-600 text-white rounded-xl text-xs font-black tracking-widest uppercase hover:bg-red-700 transition-all shadow-lg shadow-red-600/20 active:scale-95">STOCKED OUT (-)</button>
                            </div>
                            <p class="text-[9px] text-gray-400 font-bold text-center uppercase tracking-widest">All actions are recorded for audit compliance</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Idle State --}}
            <div id="idle-container" class="bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700 p-12 text-center group">
                <div class="w-16 h-16 mx-auto bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center text-gray-300 dark:text-gray-600 group-hover:scale-110 transition-transform">
                    <i data-feather="pocket" class="w-8 h-8"></i>
                </div>
                <h4 class="mt-6 text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Scanner Ready</h4>
                <p class="mt-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-loose">Position the asset QR code within the frame to authenticate and manage inventory levels.</p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scan-line {
    0% { top: 0; }
    50% { top: 100%; }
    100% { top: 0; }
}
.animate-scan-line {
    animation: scan-line 4s linear infinite;
}
</style>

{{-- Scanner Library --}}
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let html5QrCode;
    let currentSku = null;

    function startScanner() {
        document.getElementById('scanner-container').innerHTML = '<div id="reader" class="w-full h-full rounded-2xl overflow-hidden"></div>';
        
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                console.error(err);
                Swal.fire('Hardware Error', 'Could not initialize camera feed.', 'error');
            });
    }

    function onScanSuccess(decodedText, decodedResult) {
        processInput(decodedText);
        // Optional: stop scanner after success or keep going
    }

    function lookupSku() {
        const sku = document.getElementById('manual-sku').value;
        if (sku) processInput(sku);
    }

    function processInput(input) {
        const warehouseId = document.getElementById('warehouse-id').value;
        
        fetch("{{ route('warehouse.process-scan') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_code: input, warehouse_id: warehouseId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                currentSku = data.item.sku;
                document.getElementById('item-name').innerText = data.item.name;
                document.getElementById('item-sku').innerText = 'SKU: ' + data.item.sku;
                document.getElementById('item-stock').innerText = data.item.stock;
                document.getElementById('item-price').innerText = 'Rp ' + data.item.price;
                document.getElementById('item-warehouse').innerText = 'Warehouse: ' + data.warehouse_name;
                
                document.getElementById('idle-container').classList.add('hidden');
                document.getElementById('result-container').classList.remove('hidden');
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
                Toast.fire({ icon: 'success', title: 'Asset Authenticated' });
            } else {
                Swal.fire('Scan Failed', data.message, 'error');
            }
        });
    }

    function adjust(type) {
        const qty = document.getElementById('adjust-qty').value;
        const warehouseId = document.getElementById('warehouse-id').value;
        
        if (!currentSku) return;

        fetch("{{ route('warehouse.adjust') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                sku: currentSku,
                warehouse_id: warehouseId,
                type: type,
                quantity: qty
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('item-stock').innerText = data.new_stock;
                Swal.fire({
                    icon: 'success',
                    title: 'System Synced',
                    text: 'Inventory levels updated successfully.',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
