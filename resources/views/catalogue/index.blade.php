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
        <p class="text-gray-600 dark:text-gray-400">Manage your product catalog</p>
    </div>
    <div class="flex gap-2">
        <button onclick="openImportModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
            <i data-feather="upload" class="w-4 h-4"></i>
            Import
        </button>
        <a href="{{ route('catalogue.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i>
            Add Product
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or SKU..." class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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

{{-- Import Progress Bar --}}
<div id="import-progress-container" class="hidden mb-6 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Import Progress</span>
        <span id="import-progress-text" class="text-sm text-gray-600 dark:text-gray-400">Processing...</span>
    </div>
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
        <div id="import-progress-bar" class="bg-indigo-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
    </div>
</div>

{{-- Data Table View --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
    {{-- Bulk Actions Bar --}}
    <div id="bulk-actions-bar" class="hidden px-6 py-3 bg-indigo-50 dark:bg-indigo-900/20 border-b border-indigo-200 dark:border-indigo-800">
        <div class="flex items-center justify-between">
            <span id="selected-count" class="text-sm font-medium text-indigo-900 dark:text-indigo-100"></span>
            <button onclick="bulkDelete()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                <i data-feather="trash-2" class="w-4 h-4 inline mr-1"></i>
                Delete Selected
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left">
                        <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" 
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Attributes</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="item-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" 
                                   value="{{ $item->id }}" onchange="updateBulkActions()">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                @if($item->primaryImage)
                                    <img src="{{ asset('storage/' . $item->primaryImage->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <i data-feather="image" class="w-6 h-6 text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $item->sku }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $item->description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->category)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                    {{ $item->category->name }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-wrap gap-1">
                                @if($item->tags)
                                    @foreach(array_slice($item->tags_array, 0, 2) as $tag)
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded">{{ trim($tag) }}</span>
                                    @endforeach
                                    @if(count($item->tags_array) > 2)
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs rounded">+{{ count($item->tags_array) - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('catalogue.show', $item) }}" class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400" title="View">
                                    <i data-feather="eye" class="w-5 h-5"></i>
                                </a>
                                <a href="{{ route('catalogue.edit', $item) }}" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400" title="Edit">
                                    <i data-feather="edit-2" class="w-5 h-5"></i>
                                </a>
                                <form action="{{ route('catalogue.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400" title="Delete">
                                        <i data-feather="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i data-feather="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">No products found.</p>
                            <a href="{{ route('catalogue.create') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-500 font-medium">
                                Add your first product
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($items->hasPages())
    <div class="mt-6">
        {{ $items->links() }}
    </div>
@endif

@include('catalogue.partials.catalogue-importmodal')

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Success Notification --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Import Started!',
        text: '{{ session('success') }}',
        showConfirmButton: true,
        confirmButtonColor: '#4F46E5',
        timer: 5000,
        timerProgressBar: true
    });

    // Start polling if import job ID exists
    @if(session('import_job_id'))
        const importJobId = {{ session('import_job_id') }};
        startImportPolling(importJobId);
    @endif
</script>
@endif

{{-- Import Progress Polling Script --}}
<script>
let pollingInterval = null;

function startImportPolling(importJobId) {
    // Show progress bar
    const progressContainer = document.getElementById('import-progress-container');
    const progressBar = document.getElementById('import-progress-bar');
    const progressText = document.getElementById('import-progress-text');
    
    progressContainer.classList.remove('hidden');

    // Poll every 2 seconds
    pollingInterval = setInterval(() => {
        fetch(`{{ route('catalogue.import.status') }}?import_job_id=${importJobId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'processing') {
                    progressBar.style.width = data.progress + '%';
                    progressText.textContent = `Processing... ${data.processed_rows}/${data.total_rows} rows (${data.progress}%)`;
                } else if (data.status === 'completed') {
                    clearInterval(pollingInterval);
                    progressBar.style.width = '100%';
                    progressText.textContent = 'Import completed!';
                    
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Completed!',
                            text: `Successfully imported ${data.total_rows} products from ${data.file_name}`,
                            confirmButtonColor: '#4F46E5'
                        }).then(() => {
                            window.location.reload();
                        });
                    }, 500);
                } else if (data.status === 'failed') {
                    clearInterval(pollingInterval);
                    progressContainer.classList.add('hidden');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Failed',
                        text: data.error_message || 'An error occurred during import',
                        confirmButtonColor: '#4F46E5'
                    });
                }
            })
            .catch(error => {
                console.error('Polling error:', error);
            });
    }, 2000);
}

// Bulk Delete Functions
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const bulkBar = document.getElementById('bulk-actions-bar');
    const selectedCount = document.getElementById('selected-count');
    const selectAll = document.getElementById('select-all');
    
    if (checkboxes.length > 0) {
        bulkBar.classList.remove('hidden');
        selectedCount.textContent = `${checkboxes.length} item${checkboxes.length > 1 ? 's' : ''} selected`;
        
        // Update select-all checkbox state
        const allCheckboxes = document.querySelectorAll('.item-checkbox');
        selectAll.checked = checkboxes.length === allCheckboxes.length;
        selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    } else {
        bulkBar.classList.add('hidden');
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
    
    if (typeof feather !== 'undefined') feather.replace();
}

function bulkDelete() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (ids.length === 0) return;
    
    Swal.fire({
        title: 'Delete Selected Items?',
        text: `Are you sure you want to delete ${ids.length} item${ids.length > 1 ? 's' : ''}? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, delete!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('catalogue.bulk-delete') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message || `${ids.length} item${ids.length > 1 ? 's' : ''} deleted successfully.`,
                        confirmButtonColor: '#4F46E5'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete items',
                        confirmButtonColor: '#4F46E5'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting items',
                    confirmButtonColor: '#4F46E5'
                });
            });
        }
    });
}
</script>

@endsection
