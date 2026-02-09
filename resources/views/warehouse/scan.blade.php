@extends('layouts.app', [
    'title' => 'Scan Item',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Warehouse', 'url' => route('warehouse.index')],
        ['name' => 'Scan', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                Scan Item QR Code
            </h2>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="p-6">
            {{-- Warehouse Selector --}}
            <div class="mb-6">
                <label for="warehouse-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Warehouse Context</label>
                <select id="warehouse-select" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="resetScanner()">
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Scan results and adjustments will apply to this warehouse.</p>
            </div>

            {{-- Scanner Container --}}
            <div id="scanner-wrapper" class="mx-auto max-w-lg mb-6 text-center">
                
                <div id="start-btn-container" class="py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <i data-feather="camera" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Ready to Scan</h3>
                    <p class="text-gray-500 mb-6 max-w-xs mx-auto">Please allow camera access when prompted to start scanning codes.</p>
                    
                    <button onclick="requestCameraPermission()" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i data-feather="camera" class="w-5 h-5 mr-2"></i>
                        Start Scanning
                    </button>
                </div>

                <div id="scanner-container" class="hidden relative rounded-lg overflow-hidden bg-black">
                     <div id="reader" class="w-full"></div>
                     <button onclick="stopScanner()" class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full shadow hover:bg-red-700 z-10" title="Stop Camera">
                        <i data-feather="x" class="w-4 h-4"></i>
                     </button>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Point your camera at a Product QR Code</p>
            </div>

            {{-- Result Container --}}
            <div id="scan-result" class="mt-6 hidden transition-all duration-300 ease-in-out">
                <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-feather="check-circle" class="h-5 w-5 text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-green-800 dark:text-green-200">Item Found!</h3>
                            
                            <div class="mt-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Product Name</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white" id="res-name">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</p>
                                        <p class="text-base font-mono text-gray-900 dark:text-white" id="res-sku">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Stock at <span id="res-wh-name" class="font-bold text-primary-600">Warehouse</span>
                                        </p>
                                        <p class="text-2xl font-bold text-primary-600" id="res-stock">-</p>
                                    </div>
                                    <div>
                                         <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Price</p>
                                        <p class="text-base text-gray-900 dark:text-white">Rp <span id="res-price">-</span></p>
                                    </div>
                                </div>
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <a href="#" id="res-link" class="text-primary-600 hover:text-primary-500 font-medium text-sm">View Details &rarr;</a>
                                        
                                        <div class="flex items-center gap-2">
                                            <button onclick="adjustStock('out')" class="px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 text-sm font-medium dark:bg-red-900/30 dark:text-red-400">
                                                - Out
                                            </button>
                                            <input type="number" id="adjust-qty" value="1" min="1" class="w-16 text-center text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <button onclick="adjustStock('in')" class="px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 text-sm font-medium dark:bg-green-900/30 dark:text-green-400">
                                                + In
                                            </button>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <button onclick="resetScanner()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Scan Another Item
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
{{-- SweetAlert2 loaded via Vite --}}
<script>
    // Use window property to avoid redeclaration errors in SPA/Turbo
    window.html5QrCode = window.html5QrCode || null;

    function onScanSuccess(decodedText, decodedResult) {
        if(window.html5QrCode) {
            window.html5QrCode.stop().then(_ => {
                window.html5QrCode.clear();
                toggleScannerUI(false);
                fetchItemDetails(decodedText);
            }).catch(error => {
                console.error("Failed to stop scanner", error);
            });
        }
    }

    function onScanFailure(error) {
        // handle scan failure
    }

    window.requestCameraPermission = function() {
        Swal.fire({
            title: 'Camera Access Needed',
            text: 'To scan QR codes, please allow access to your camera in the next prompt.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'I Understand, Enable Camera',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                startScanner();
            }
        });
    }

    function startScanner() {
        const warehouseId = document.getElementById('warehouse-select').value;
        if(!warehouseId) {
            Swal.fire('Error', 'Please select a warehouse first', 'warning');
            return;
        }

        // Show loading
        document.getElementById('scanner-container').classList.remove('hidden');
        document.getElementById('start-btn-container').classList.add('hidden');
        
        // Clean up if exists
        if(window.html5QrCode) {
             try { window.html5QrCode.clear(); } catch(e){}
        }

        window.html5QrCode = new Html5Qrcode("reader");

        // Prefer back camera
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        window.html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .catch(err => {
            console.error(err);
            document.getElementById('scanner-container').classList.add('hidden');
            document.getElementById('start-btn-container').classList.remove('hidden');
            
            let msg = 'Failed to access camera.';
            if (err.name === 'NotAllowedError' || err.message.includes('permission')) {
                msg = 'Camera permission denied. Please allow camera access in your browser settings and try again.';
            }
            
            Swal.fire('Camera Error', msg, 'error');
        });
    }

    window.stopScanner = function() {
        if(window.html5QrCode) {
            window.html5QrCode.stop().then(() => {
                window.html5QrCode.clear();
                toggleScannerUI(false);
            }).catch(err => console.log(err));
        } else {
            toggleScannerUI(false);
        }
    }

    window.toggleScannerUI = function(show) {
        if(show) {
            document.getElementById('scanner-container').classList.remove('hidden');
            document.getElementById('start-btn-container').classList.add('hidden');
        } else {
            document.getElementById('scanner-container').classList.add('hidden');
            document.getElementById('start-btn-container').classList.remove('hidden');
        }
    }

    window.resetScanner = function() {
        // Just reset UI to start state
        document.getElementById('scan-result').classList.add('hidden');
        toggleScannerUI(false);
    }

    function fetchItemDetails(sku) {
        const warehouseId = document.getElementById('warehouse-select').value;
        if(!warehouseId) {
            Swal.fire('Error', 'Please select a warehouse first', 'warning');
            resetScanner();
            return;
        }

        Swal.fire({
            title: 'Processing...',
            text: 'Looking up item ' + sku,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        // Parse JSON if needed (for our generated QRs)
        let parsedSku = sku;
        try {
            const json = JSON.parse(sku);
            if(json.sku) parsedSku = json.sku;
        } catch(e) {
            // Not JSON, use as is
        }

        fetch('{{ route("warehouse.process-scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_code: parsedSku, warehouse_id: warehouseId }) 
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if(data.status === 'success') {
                displayResult(data.item, data.warehouse_name);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Item Not Found',
                    text: 'No item found with SKU: ' + parsedSku,
                    confirmButtonText: 'Try Again'
                }).then((result) => {
                     startScanner();
                });
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Something went wrong: ' + error, 'error');
            setTimeout(() => startScanner(), 1000);
        });
    }

    function displayResult(item, warehouseName) {
        toggleScannerUI(false);
        
        document.getElementById('res-name').innerText = item.name;
        document.getElementById('res-sku').innerText = item.sku;
        document.getElementById('res-stock').innerText = item.stock + ' ' + item.unit;
        document.getElementById('res-wh-name').innerText = warehouseName || 'Selected Warehouse';
        document.getElementById('res-price').innerText = item.price;
        document.getElementById('res-link').href = item.product_url;

        document.getElementById('scan-result').classList.remove('hidden');
    }

    // Function to handle stock adjustment... 
    // (Defining it globally to ensure it's accessible)
    window.adjustStock = function(type) {
        const sku = document.getElementById('res-sku').innerText;
        const qty = document.getElementById('adjust-qty').value;
        const warehouseId = document.getElementById('warehouse-select').value;

        if(!warehouseId) {
            Swal.fire('Error', 'Please select a warehouse first', 'warning');
            return;
        }

        Swal.fire({
            title: type === 'in' ? 'Stock In?' : 'Stock Out?',
            text: `Adjust stock by ${qty} ${type === 'in' ? '(Addition)' : '(Reduction)'} at this warehouse`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: type === 'in' ? '#10B981' : '#EF4444',
            confirmButtonText: 'Yes, Adjust'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("warehouse.adjust") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ sku: sku, type: type, quantity: qty, warehouse_id: warehouseId })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        Swal.fire('Success', data.message, 'success');
                        const unit = document.getElementById('res-stock').innerText.split(' ')[1] || '';
                        document.getElementById('res-stock').innerText = data.new_stock + ' ' + unit;
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Failed to adjust stock', 'error'));
            }
        });
    }

    // Initialize on load and Turbo load
    document.addEventListener('turbo:load', () => {
        // If we want to reset UI on nav
        resetScanner();
    });
</script>
@endsection