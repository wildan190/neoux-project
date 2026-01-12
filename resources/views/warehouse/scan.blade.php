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
            <div id="reader" width="600px" class="mx-auto border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-900 dark:border-gray-700"></div>
            
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let html5QrcodeScanner;
    
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning to prevent multiple triggers
        if(html5QrcodeScanner) {
            html5QrcodeScanner.clear().then(_ => {
                // Fetch item details
                fetchItemDetails(decodedText);
            }).catch(error => {
                console.error("Failed to clear scanner", error);
            });
        }
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // console.warn(`Code scan error = ${error}`);
    }

    function startScanner() {
        document.getElementById('scan-result').classList.add('hidden');
        document.getElementById('reader').classList.remove('hidden');

        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    function resetScanner() {
        startScanner();
    }

    function fetchItemDetails(sku) {
        const warehouseId = document.getElementById('warehouse-select').value;
        if(!warehouseId) {
            Swal.fire('Error', 'Please select a warehouse first', 'warning');
            return;
        }

        // Show loading state if needed
        Swal.fire({
            title: 'Processing...',
            text: 'Looking up item ' + sku,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        fetch('{{ route("warehouse.process-scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_code: sku, warehouse_id: warehouseId }) // Sent warehouse_id
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
                    text: 'No item found with SKU: ' + sku,
                    confirmButtonText: 'Try Again'
                }).then((result) => {
                     startScanner();
                });
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Something went wrong', 'error');
             startScanner();
        });
    }

    function displayResult(item, warehouseName) {
        document.getElementById('reader').classList.add('hidden'); // Hide scanner
        
        document.getElementById('res-name').innerText = item.name;
        document.getElementById('res-sku').innerText = item.sku;
        document.getElementById('res-stock').innerText = item.stock + ' ' + item.unit;
        document.getElementById('res-wh-name').innerText = warehouseName || 'Selected Warehouse';
        document.getElementById('res-price').innerText = item.price;
        document.getElementById('res-link').href = item.product_url;

        document.getElementById('scan-result').classList.remove('hidden');
    }

    function adjustStock(type) {
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
                        // Update stock display
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

    document.addEventListener('DOMContentLoaded', function () {
        startScanner();
    });
</script>
@endsection
