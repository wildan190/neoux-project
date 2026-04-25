@extends('layouts.app', [
    'title' => 'Inventory Scanner',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'WMS', 'url' => route('warehouse.index')],
        ['name' => 'Scanner', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-20">
    {{-- Terminal Header --}}
    <div class="bg-gray-900 rounded-2xl p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i data-feather="maximize" class="w-24 h-24"></i>
        </div>
        <div class="relative z-10 space-y-2">
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

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">

        {{-- LEFT PANEL: Instructions --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div class="w-8 h-8 bg-primary-50 dark:bg-primary-900/30 rounded-xl flex items-center justify-center text-primary-600">
                        <i data-feather="book-open" class="w-4 h-4"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">How To Scan</p>
                </div>
                <ol class="space-y-4">
                    @foreach([
                        ['icon'=>'layers','title'=>'Select Warehouse','desc'=>'Choose the target warehouse node from the dropdown on the right.'],
                        ['icon'=>'camera','title'=>'Init Camera','desc'=>'Click "Initialize Camera" and allow browser camera access.'],
                        ['icon'=>'maximize','title'=>'Aim at QR','desc'=>'Point camera at the item QR code until it gets authenticated.'],
                        ['icon'=>'edit-2','title'=>'Or Manual Entry','desc'=>'If camera fails, type the SKU code directly in the input field.'],
                        ['icon'=>'check-circle','title'=>'Adjust Stock','desc'=>'After authentication, use the IN/OUT buttons to update stock levels.'],
                    ] as $i => $step)
                    <li class="flex gap-3">
                        <div class="w-6 h-6 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900 text-[9px] font-black shrink-0 mt-0.5">{{ $i+1 }}</div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest mb-0.5">{{ $step['title'] }}</p>
                            <p class="text-[10px] text-gray-400 font-medium leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ol>
            </div>

            <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-900/30 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <i data-feather="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                    <p class="text-[10px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-widest">Note</p>
                </div>
                <p class="text-[10px] font-medium text-amber-700/80 dark:text-amber-400/80 leading-relaxed">All stock adjustments are permanently recorded in the audit log. Ensure quantity is correct before submitting.</p>
            </div>
        </div>

        {{-- CENTER: Scanner --}}
        <div class="xl:col-span-2 flex flex-col gap-6">
            {{-- 1. Selection Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Target Warehouse</h3>
                        <p class="text-[9px] text-gray-500 font-bold uppercase tracking-tight">Active Node Selection</p>
                    </div>
                    <div class="w-full md:w-72">
                        <select id="warehouse-id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl px-4 py-3 text-xs font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none uppercase tracking-widest">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 2. Scanner Visualizer --}}
            <div class="bg-gray-900 dark:bg-black rounded-3xl p-4 shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 z-10 flex items-center justify-center p-8 md:p-16 pointer-events-none opacity-50">
                    <div class="w-full h-full border border-white/20 rounded-3xl relative">
                        <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-primary-500 rounded-tl-lg"></div>
                        <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-primary-500 rounded-tr-lg"></div>
                        <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-primary-500 rounded-bl-lg"></div>
                        <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-primary-500 rounded-br-lg"></div>
                    </div>
                </div>

                <div id="scanner-container" class="w-full h-[320px] bg-gray-900 flex flex-col items-center justify-center space-y-4">
                    <div class="w-14 h-14 rounded-full bg-primary-500/10 flex items-center justify-center text-primary-500 animate-pulse border border-primary-500/20">
                        <i data-feather="maximize" class="w-6 h-6"></i>
                    </div>
                    <button onclick="startScanner()" class="px-8 py-3 bg-primary-600 text-white rounded-xl text-[10px] font-black tracking-[0.2em] uppercase hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/30">
                        OPEN CAMERA TERMINAL
                    </button>
                    <div id="reader" class="w-full h-full hidden overflow-hidden rounded-2xl"></div>
                </div>
            </div>

            {{-- 3. Interaction & Data Unit --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Manual Command Center --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Direct SKU Override</h3>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-tight mb-6">Manually authenticate via barcode data</p>
                    </div>
                    <div class="flex gap-2 bg-gray-50 dark:bg-gray-900 p-1 rounded-xl border border-gray-100 dark:border-gray-700">
                        <input type="text" id="manual-sku" placeholder="SKU-XXXX-XXXX" class="w-full bg-transparent border-none px-3 py-2 text-[11px] font-black text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0 outline-none uppercase">
                        <button onclick="lookupSku()" class="px-5 py-2 bg-gray-900 dark:bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all">EXEC</button>
                    </div>
                </div>

                {{-- Asset Result Unit --}}
                <div class="relative min-h-[160px]">
                    {{-- Result Display --}}
                    <div id="result-container" class="hidden absolute inset-0 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-300">
                        <div class="bg-emerald-600 px-4 py-2 flex items-center justify-between">
                            <span class="text-[9px] font-black text-white uppercase tracking-widest">Asset Authenticated</span>
                            <div class="w-4 h-4 bg-white/20 rounded-full flex items-center justify-center text-white">
                                <i data-feather="check" class="w-2.5 h-2.5"></i>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center text-emerald-600 shrink-0">
                                    <i data-feather="package" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 id="item-name" class="text-[11px] font-black text-gray-900 dark:text-white uppercase truncate">Product Name</h4>
                                    <p id="item-sku" class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest">SKU: PROD-123</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 mt-4">
                                <input type="number" id="adjust-qty" value="1" min="1" class="w-14 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-lg py-2 text-center text-xs font-black text-gray-900 dark:text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                                <button onclick="adjust('in')" class="flex-1 py-2 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20">STOCKED (+)</button>
                                <button onclick="adjust('out')" class="flex-1 py-2 bg-red-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-600/20">ISSUED (-)</button>
                            </div>
                        </div>
                    </div>

                    {{-- Idle Display --}}
                    <div id="idle-container" class="absolute inset-0 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center p-6 text-center group">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-300 dark:text-gray-600 mb-3 group-hover:scale-110 transition-transform">
                            <i data-feather="target" class="w-5 h-5"></i>
                        </div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Awaiting Scanner Input</p>
                    </div>
                </div>
            </div>
        </div> {{-- end center col --}}

        {{-- RIGHT PANEL: Warehouse Stats --}}
        <div class="space-y-6">
            <div class="bg-gray-900 dark:bg-black rounded-2xl border border-gray-800 p-6 text-white relative overflow-hidden">
                <div class="absolute bottom-0 right-0 -mb-8 -mr-8 w-32 h-32 bg-primary-600/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-white/5 relative z-10">
                    <div class="w-8 h-8 bg-white/5 rounded-xl flex items-center justify-center text-primary-400">
                        <i data-feather="layers" class="w-4 h-4"></i>
                    </div>
                    <p class="text-[10px] font-black text-white uppercase tracking-widest">Warehouse Nodes</p>
                </div>
                <div class="space-y-3 relative z-10">
                    @foreach($warehouses as $wh)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl">
                        <div>
                            <p class="text-[10px] font-black text-white uppercase tracking-widest">{{ $wh->name }}</p>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">{{ $wh->code }}</p>
                        </div>
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                    @endforeach
                    @if($warehouses->isEmpty())
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest text-center py-4">No warehouses registered</p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div class="w-8 h-8 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                        <i data-feather="shield" class="w-4 h-4"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Scan Modes</p>
                </div>
                <div class="space-y-3">
                    @foreach([
                        ['color'=>'green','label'=>'Stock In','desc'=>'Add received goods to warehouse'],
                        ['color'=>'red','label'=>'Stock Out','desc'=>'Remove dispatched goods from stock'],
                    ] as $mode)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl">
                        <div class="w-3 h-3 rounded-full bg-{{ $mode['color'] }}-500 shrink-0"></div>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">{{ $mode['label'] }}</p>
                            <p class="text-[9px] font-medium text-gray-400">{{ $mode['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
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
