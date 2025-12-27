@extends('layouts.app', [
    'title' => 'Catalogue',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Catalogue', 'url' => '#']
    ]
])

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Product Catalogue</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage your products and their variants</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('catalogue.download-template') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
            <i data-feather="download" class="w-4 h-4"></i>
            Template
        </a>
        <button onclick="openImportModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
            <i data-feather="upload" class="w-4 h-4"></i>
            Import
        </button>
        <a href="{{ route('catalogue.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i>
            Add Product
        </a>
    </div>
</div>

@include('catalogue.partials.catalogue-import-modal')

<script>
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
        resetImportForm();
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        resetImportForm();
    }

    function resetImportForm() {
        document.getElementById('importForm').reset();
        document.getElementById('filename-display').innerText = '';
        document.getElementById('step-1').classList.remove('hidden');
        document.getElementById('step-2').classList.add('hidden');
        document.getElementById('btn-preview').classList.remove('hidden');
        document.getElementById('btn-import').classList.add('hidden');
        document.getElementById('import-progress').classList.add('hidden');
        
        // Clear table
        const table = document.getElementById('preview-table');
        table.querySelector('thead').innerHTML = '';
        table.querySelector('tbody').innerHTML = '';
    }

    document.getElementById('file-upload').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            document.getElementById('filename-display').innerText = e.target.files[0].name;
            // Auto preview could be enabled here if desired
        }
    });

    async function previewImport() {
        const fileInput = document.getElementById('file-upload');
        if (fileInput.files.length === 0) {
            Swal.fire('Error', 'Please select a file first.', 'error');
            return;
        }

        const formData = new FormData(document.getElementById('importForm'));
        const btn = document.getElementById('btn-preview');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

        try {
            const response = await fetch('{{ route("catalogue.import.preview") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (!response.ok) throw new Error(result.message || 'Preview failed');

            // Show Step 2
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            document.getElementById('btn-preview').classList.add('hidden');
            document.getElementById('btn-import').classList.remove('hidden');

            renderPreviewTable(result.preview);

        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Preview Data';
        }
    }

    function renderPreviewTable(data) {
        const table = document.getElementById('preview-table');
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        
        thead.innerHTML = '';
        tbody.innerHTML = '';

        if (data.length === 0) return;

        // Headers (First row)
        const headers = data[0]; 
        let headerHtml = '<tr>';
        headers.forEach(h => {
             headerHtml += `<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">${h || ''}</th>`;
        });
        headerHtml += '</tr>';
        thead.innerHTML = headerHtml;
        const rows = data.slice(1); 
        rows.forEach(row => {
            let rowHtml = '<tr>';
            row.forEach(cell => {
                rowHtml += `<td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 border-b border-gray-100 dark:border-gray-700">${cell || ''}</td>`;
            });
            rowHtml += '</tr>';
            tbody.insertAdjacentHTML('beforeend', rowHtml);
        });
    }

    async function submitImport() {
        const formData = new FormData(document.getElementById('importForm'));
        const btn = document.getElementById('btn-import');
        const progressContainer = document.getElementById('import-progress');

        btn.disabled = true;
        btn.innerText = 'Uploading...';
        progressContainer.classList.remove('hidden');

        try {
            const response = await fetch('{{ route("catalogue.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (!response.ok) throw new Error(result.message || 'Import failed');

            btn.innerText = 'Processing...';
            
            // Poll for status
            checkStatus(result.job_id);

        } catch (error) {
            Swal.fire('Error', error.message, 'error');
            // resetImportForm(); // Don't reset completely so they can try again or see error
            btn.disabled = false;
            btn.innerText = 'Confirm & Import';
        }
    }
</script>


{{-- Filters --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        <select name="category" class="rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($products as $product)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden group border border-gray-100 dark:border-gray-700">
            {{-- Image Thumbnail (First Item's image or placeholder) --}}
            <div class="h-48 bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
                @php
                    $firstItem = $product->items->first();
                    $image = $firstItem ? $firstItem->primaryImage : null;
                @endphp
                
                @if($image)
                     <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='{{ asset('assets/img/products/default-product.png') }}'">
                @else
                    <img src="{{ asset('assets/img/products/default-product.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-50">
                @endif
                
                <div class="absolute top-2 right-2">
                     @if($product->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                    @endif
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-xs text-primary-600 dark:text-primary-400 font-medium mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate" title="{{ $product->name }}">{{ $product->name }}</h3>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 h-10">{{ $product->description }}</p>
                
                <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        <span class="font-semibold">{{ $product->items->count() }}</span> Variants
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('catalogue.edit', $product) }}" class="p-2 text-gray-500 hover:text-primary-600 bg-gray-50 hover:bg-primary-50 rounded-lg transition dark:bg-gray-700 dark:hover:bg-primary-900/30">
                            <i data-feather="edit-2" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('catalogue.show', $product) }}" class="px-3 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition flex items-center gap-1">
                            Details <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                <i data-feather="package" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No products found</h3>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Get started by creating your first product.</p>
            <div class="mt-6">
                <a href="{{ route('catalogue.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    <i data-feather="plus" class="-ml-1 mr-2 w-5 h-5"></i>
                    Add Product
                </a>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($products->hasPages())
    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection
