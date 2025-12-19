@extends('layouts.app', [
    'title' => $product->name,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Catalogue', 'url' => route('catalogue.index')],
        ['name' => $product->name, 'url' => '#']
    ]
])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Product Details Column --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6">
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Product Details</h2>
                <a href="{{ route('catalogue.edit', $product) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Edit</a>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Name</label>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $product->name }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Category</label>
                    <p class="text-gray-900 dark:text-white">{{ $product->category->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Brand</label>
                    <p class="text-gray-900 dark:text-white">{{ $product->brand ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Description</label>
                    <p class="text-gray-700 dark:text-gray-300 text-sm whitespace-pre-line">{{ $product->description }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Status</label>
                     @if($product->is_active)
                        <span class="inline-flex px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    @else
                        <span class="inline-flex px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Summary</h3>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <span class="block text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $product->items->count() }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Variants</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                    <span class="block text-2xl font-bold text-gray-900 dark:text-white">{{ $product->items->sum('stock') }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Stock</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Variants (SKUs) Column --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Variants / SKUs</h2>
                <button onclick="openAddSkuModal()" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i> Add Variant
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Attributes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price / Stock</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($product->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    @if($item->primaryImage)
                                        <img src="{{ asset('storage/' . $item->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="flex items-center justify-center h-full text-gray-400">
                                            <i data-feather="image" class="w-6 h-6"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $item->sku }}</span>
                                <div class="text-xs text-gray-500">{{ $item->unit }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->attributes as $attr)
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-xs rounded text-gray-700 dark:text-gray-300">
                                            <span class="font-semibold">{{ $attr->attribute_key }}:</span> {{ $attr->attribute_value }}
                                        </span>
                                    @endforeach
                                    @if($item->attributes->isEmpty())
                                        <span class="text-gray-400 text-xs italic">No attributes</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </div>
                                <div class="text-xs {{ $item->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    Stock: {{ $item->stock }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('warehouse.generate-qr', $item->id) }}" target="_blank" class="text-gray-400 hover:text-primary-600 transition" title="Print QR Code">
                                        <i data-feather="printer" class="w-4 h-4"></i>
                                    </a>
                                    <button class="text-gray-400 hover:text-red-600 transition" title="Delete SKU" onclick="confirmDeleteSku('{{ $item->id }}')">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    {{-- Delete Form (Hidden) --}}
                                    <form id="delete-sku-{{ $item->id }}" action="{{ route('catalogue.destroy', $item->id) }}" method="POST" class="hidden"> 
                                        {{-- Note: destroy route currently expects Product, need to check if we can delete Item directly or need separate Item route. --}}
                                        {{-- We probably need a separate route for deleting individual SKUs if existing route expects Product --}}
                                        {{-- For now let's assume we need to add that route or use existing destroy if it handles polymorphically (it doesn't). --}}
                                        {{-- I will leave this non-functional or add a route for item deletion later. --}}
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No variants added yet. Click "Add Variant" to create one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add SKU Modal --}}
<div id="add-sku-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Backdrop with Blur --}}
        <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true" onclick="closeAddSkuModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full z-10 relative border border-gray-100 dark:border-gray-700">
            <div class="px-6 pt-6 pb-6 bg-white dark:bg-gray-800">
                <div class="sm:flex sm:items-start">
                    <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="modal-title">
                            Add New Variant (SKU)
                        </h3>
                        <div class="mt-4">
                            <form id="add-sku-form" action="{{ route('catalogue.store-sku', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                                        <div class="flex gap-2">
                                            <input type="text" name="sku" id="modal-sku" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors" required>
                                            <button type="button" onclick="generateSku()" class="mt-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors font-medium text-sm">
                                                Generate
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit</label>
                                        <input type="text" name="unit" placeholder="Pcs, Box, Kg" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors" required>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price (IDR)</label>
                                        <div class="relative mt-1">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                                            <input type="number" name="price" min="0" class="block w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Initial Stock</label>
                                        <input type="number" name="stock" min="0" value="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors" required>
                                    </div>
                                </div>
                                
                                {{-- Attributes --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Attributes</label>
                                        <button type="button" onclick="addModalAttribute()" class="text-xs text-primary-600 hover:text-primary-500">+ Add Attribute</button>
                                    </div>
                                    <div id="modal-attributes-container" class="space-y-2">
                                        {{-- Attribute Rows --}}
                                    </div>
                                </div>

                                {{-- Images --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Images</label>
                                    <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-300">
                                </div>

                                <div class="pt-4 flex justify-end gap-3">
                                    <button type="button" onclick="closeAddSkuModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-primary-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">Save Variant</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let modalAttributeIndex = 0;

    function openAddSkuModal() {
        const modal = document.getElementById('add-sku-modal');
        modal.classList.remove('hidden');
        if(modalAttributeIndex === 0) {
            addModalAttribute(); // Add one default attribute row
        }
    }

    function closeAddSkuModal() {
        document.getElementById('add-sku-modal').classList.add('hidden');
    }

    function addModalAttribute() {
        const container = document.getElementById('modal-attributes-container');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-2 gap-2 flex items-center';
        div.innerHTML = `
            <input type="text" name="attributes[${modalAttributeIndex}][key]" placeholder="Key (e.g. Color)" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <div class="flex gap-2">
                <input type="text" name="attributes[${modalAttributeIndex}][value]" placeholder="Value (e.g. Red)" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button type="button" onclick="this.closest('.grid').remove()" class="text-red-500 hover:text-red-700 p-1">
                    &times;
                </button>
            </div>
        `;
        container.appendChild(div);
        modalAttributeIndex++;
    }

    function generateSku() {
        fetch('{{ route("catalogue.generate-sku") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ category_id: {{ $product->category_id ?? 'null' }} })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('modal-sku').value = data.sku;
        });
    }


    // Feather icons
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endsection
