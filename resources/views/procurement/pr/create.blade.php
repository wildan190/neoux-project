@extends('layouts.app', [
'title' => 'Create Purchase Requisition',
'breadcrumbs' => [
['name' => 'Procurement', 'url' => route('procurement.pr.index')],
['name' => 'Create Request', 'url' => '#']
]
])

@push('styles')
{{-- Custom styles if needed --}}
@endpush

@section('content')
<form action="{{ route('procurement.pr.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="pr-form">
    @csrf

    {{-- Error/Success Messages --}}
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex items-center">
                <i data-feather="alert-circle" class="w-5 h-5 text-red-500 mr-3"></i>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex items-start">
                <i data-feather="alert-circle" class="w-5 h-5 text-red-500 mr-3 mt-0.5"></i>
                <div>
                    <p class="text-sm font-bold text-red-800 dark:text-red-200 mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- General Information Card --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">General Information</h3>
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Request Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required placeholder="e.g. Q4 Office Supplies Restock" value="{{ old('title') }}"
                    class="block w-full rounded-lg border @error('title') border-red-500 @else border-gray-300 @enderror px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Describe the purpose and justification for this request..."
                    class="block w-full rounded-lg border @error('description') border-red-500 @else border-gray-300 @enderror px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Document Upload Card --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Supporting Documents</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Upload documents to support your request (optional, max 10MB per file)</p>
        
        {{-- Drag & Drop Zone --}}
        <div id="dropzone" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-primary-500 dark:hover:border-primary-500 transition-colors cursor-pointer bg-gray-50 dark:bg-gray-700/30">
            <div class="flex flex-col items-center gap-3">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                    <i data-feather="upload-cloud" class="w-8 h-8 text-primary-600 dark:text-primary-400"></i>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">Drop files here or click to browse</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (max 10MB per file)</p>
                </div>
                <input type="file" name="documents[]" id="fileInput" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="hidden">
            </div>
        </div>

        {{-- File Preview List --}}
        <div id="fileList" class="mt-4 space-y-2 hidden">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Files:</p>
            <div id="fileItems" class="space-y-2"></div>
        </div>
    </div>

        {{-- Items Card --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Request Items</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add products you need to procure.</p>
                </div>
                <button type="button" onclick="addItem()"
                    class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-all shadow-sm shadow-primary-500/30">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                    Add Item
                </button>
            </div>

            <div id="items-container" class="space-y-4">
                <!-- Items will be added here -->
            </div>
            
            <div id="empty-state" class="text-center py-12 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl mt-4">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-3">
                        <i data-feather="shopping-cart" class="w-6 h-6 text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <p class="text-base font-medium text-gray-900 dark:text-white">No items added yet</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Click the "Add Item" button to start building your request.</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('procurement.pr.my-requests') }}"
                class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30">
                Submit Request
            </button>
        </div>
    </form>


    {{-- SweetAlert2 loaded via Vite --}}

    <script>
        let itemIndex = 0;
        const myItems = @json($myItems);
        const marketplaceItems = @json($marketplaceItems);

        function addItem() {
            const container = document.getElementById('items-container');
            const emptyState = document.getElementById('empty-state');
            
            emptyState.style.display = 'none';

            const itemDiv = document.createElement('div');
            itemDiv.id = `item-row-${itemIndex}`;
            itemDiv.className = 'bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700 group relative transition-all hover:border-primary-200 dark:hover:border-primary-800';
            
            // Generate options list for custom dropdown with Groups
            let optionsHtml = '';

            // Group 1: My Catalogue
            if (myItems.length > 0) {
                optionsHtml += `<li class="px-3 py-1 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-100 dark:bg-gray-800">My Catalogue</li>`;
                myItems.forEach(item => {
                    // Fallback name if product relation is missing
                    let itemName = item.name; 
                    if (!itemName && item.product) itemName = item.product.name;
                    if (!itemName) itemName = 'Unnamed Item';

                    optionsHtml += `
                        <li class="custom-select-option px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm text-gray-700 dark:text-gray-200 pl-6 border-l-2 border-transparent hover:border-primary-500" 
                            data-value="${item.id}" data-text="${itemName} (${item.sku})" data-price="${item.price}">
                            <div class="font-medium">${itemName}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex justify-between">
                                <span>${item.sku}</span>
                                <span class="font-semibold text-primary-600">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</span>
                            </div>
                        </li>
                    `;
                });
            }

            // Group 2: Marketplace
            if (marketplaceItems.length > 0) {
                optionsHtml += `<li class="px-3 py-1 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-100 dark:bg-gray-800 mt-2">Marketplace</li>`;
                marketplaceItems.forEach(item => {
                    let itemName = item.name; 
                    if (!itemName && item.product) itemName = item.product.name;
                    if (!itemName) itemName = 'Unnamed Item';
                    
                    let vendorName = item.company ? item.company.name : 'Unknown Vendor';

                    optionsHtml += `
                        <li class="custom-select-option px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm text-gray-700 dark:text-gray-200 pl-6 border-l-2 border-transparent hover:border-blue-500" 
                            data-value="${item.id}" data-text="${itemName} (${vendorName})" data-price="${item.price}">
                            <div class="font-medium flex items-center justify-between">
                                ${itemName}
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                    ${vendorName}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex justify-between mt-0.5">
                                <span>${item.sku}</span>
                                <span class="font-semibold text-blue-600">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</span>
                            </div>
                        </li>
                    `;
                });
            }

            itemDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    <div class="md:col-span-6">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Product</label>
                        <div class="custom-select-container relative">
                            <input type="hidden" name="items[${itemIndex}][catalogue_item_id]" required class="custom-select-input">
                            
                            <button type="button" class="custom-select-trigger w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 shadow-sm h-[42px] text-left text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 flex items-center justify-between group-focus:ring-2 transition-all">
                                <span class="text-gray-500 dark:text-gray-400 select-placeholder truncate mr-2">Select a product...</span>
                                <i data-feather="chevron-down" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                            </button>

                            <div class="custom-select-dropdown hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-hidden flex flex-col">
                                <div class="p-2 border-b border-gray-100 dark:border-gray-600 sticky top-0 bg-white dark:bg-gray-700 z-10">
                                    <input type="text" class="custom-select-search w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 placeholder-gray-400" placeholder="Search...">
                                </div>
                                <ul class="custom-select-options overflow-y-auto max-h-48 py-1">
                                    ${optionsHtml}
                                </ul>
                                <div class="hidden px-3 py-2 text-sm text-gray-500 dark:text-gray-400 text-center no-results">No products found</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Qty</label>
                        <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1"
                            class="block w-full rounded-lg border border-gray-300 px-3 h-[42px] shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-center">
                    </div>
                    
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Est. Price</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 text-sm">Rp</span>
                            </div>
                            <input type="number" name="items[${itemIndex}][price]" required min="0" step="0.01"
                                class="block w-full rounded-lg border border-gray-300 pl-8 pr-3 h-[42px] shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right" placeholder="0.00">
                        </div>
                    </div>

                    <div class="md:col-span-1 flex items-end justify-end h-full pb-[1px]">
                        <button type="button" onclick="removeItem(${itemIndex})" 
                            class="h-[42px] w-[42px] inline-flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Remove Item">
                            <i data-feather="trash-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(itemDiv);
            feather.replace();
            itemIndex++;
        }

        function removeItem(index) {
            const row = document.getElementById(`item-row-${index}`);
            row.remove();
            
            const container = document.getElementById('items-container');
            if (container.children.length === 0) {
                document.getElementById('empty-state').style.display = 'block';
            }
        }

        // Custom Select Logic (Event Delegation)
        document.addEventListener('click', function(e) {
            // Toggle Dropdown
            if (e.target.closest('.custom-select-trigger')) {
                const trigger = e.target.closest('.custom-select-trigger');
                const container = trigger.closest('.custom-select-container');
                const dropdown = container.querySelector('.custom-select-dropdown');
                const searchInput = container.querySelector('.custom-select-search');
                
                // Close other dropdowns
                document.querySelectorAll('.custom-select-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.add('hidden');
                });

                dropdown.classList.toggle('hidden');
                
                if (!dropdown.classList.contains('hidden')) {
                    setTimeout(() => searchInput.focus(), 100);
                }
            } 
            // Close if clicked outside
            else if (!e.target.closest('.custom-select-dropdown') && !e.target.closest('.custom-select-container')) {
                document.querySelectorAll('.custom-select-dropdown').forEach(d => d.classList.add('hidden'));
            }
        });

        // Search Functionality
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('custom-select-search')) {
                const searchTerm = e.target.value.toLowerCase();
                const container = e.target.closest('.custom-select-dropdown');
                const options = container.querySelectorAll('.custom-select-option');
                const noResults = container.querySelector('.no-results');
                let hasResults = false;

                options.forEach(option => {
                    const text = option.getAttribute('data-text').toLowerCase();
                    if (text.includes(searchTerm)) {
                        option.classList.remove('hidden');
                        hasResults = true;
                    } else {
                        option.classList.add('hidden');
                    }
                });

                if (hasResults) {
                    noResults.classList.add('hidden');
                } else {
                    noResults.classList.remove('hidden');
                }
            }
        });

        // Select Option
        document.addEventListener('click', function(e) {
            if (e.target.closest('.custom-select-option')) {
                const option = e.target.closest('.custom-select-option');
                const container = option.closest('.custom-select-container');
                const input = container.querySelector('.custom-select-input');
                const trigger = container.querySelector('.custom-select-trigger');
                const dropdown = container.querySelector('.custom-select-dropdown');
                
                const value = option.getAttribute('data-value');
                const text = option.getAttribute('data-text');
                const price = option.getAttribute('data-price');

                // Update hidden input
                input.value = value;
                
                // Auto-fill price input
                const priceInput = container.closest('.grid').querySelector('input[name$="[price]"]');
                if (priceInput && price) {
                    priceInput.value = price;
                }
                
                // Update trigger text
                const span = trigger.querySelector('span');
                span.textContent = text;
                span.classList.remove('text-gray-500', 'dark:text-gray-400', 'select-placeholder');
                span.classList.add('text-gray-900', 'dark:text-white');

                // Close dropdown
                dropdown.classList.add('hidden');
            }
        });

        // Add initial item
        document.addEventListener('DOMContentLoaded', function() {
            addItem();
        });

        // ==========================================
        // DRAG & DROP FILE UPLOAD FUNCTIONALITY
        // ==========================================
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const fileItems = document.getElementById('fileItems');
        let selectedFiles = new DataTransfer();

        // Click to browse
        dropzone.addEventListener('click', () => fileInput.click());

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            });
        });

        // Handle dropped files
        dropzone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        // Handle file input change
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            [...files].forEach(file => {
                if (validateFile(file)) {
                    selectedFiles.items.add(file);
                }
            });
            updateFileList();
            updateFileInput();
        }

        function validateFile(file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/jpeg', 'image/png'];

            if (file.size > maxSize) {
                alert(`File "${file.name}" exceeds 10MB limit`);
                return false;
            }

            if (!allowedTypes.includes(file.type)) {
                alert(`File "${file.name}" has an unsupported file type`);
                return false;
            }

            return true;
        }

        function updateFileList() {
            if (selectedFiles.files.length === 0) {
                fileList.classList.add('hidden');
                return;
            }

            fileList.classList.remove('hidden');
            fileItems.innerHTML = '';

            [...selectedFiles.files].forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 group hover:border-primary-300 transition';
                
                const fileIcon = getFileIcon(file.type);
                const fileSize = formatBytes(file.size);

                fileItem.innerHTML = `
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600">
                            <i data-feather="${fileIcon}" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${file.name}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${fileSize}</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                `;

                fileItems.appendChild(fileItem);
            });

            feather.replace();
        }

        function updateFileInput() {
            fileInput.files = selectedFiles.files;
        }

        window.removeFile = function(index) {
            const newFiles = new DataTransfer();
            [...selectedFiles.files].forEach((file, i) => {
                if (i !== index) newFiles.items.add(file);
            });
            selectedFiles = newFiles;
            updateFileList();
            updateFileInput();
        };

        function getFileIcon(fileType) {
            if (fileType.includes('pdf')) return 'file-text';
            if (fileType.includes('word')) return 'file-text';
            if (fileType.includes('sheet') || fileType.includes('excel')) return 'file-text';
            if (fileType.includes('image')) return 'image';
            return 'file';
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Form Submit with SweetAlert
        document.getElementById('pr-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation check
            if (this.checkValidity()) {
                Swal.fire({
                    title: 'Submit Request?',
                    text: "Are you sure you want to submit this purchase requisition?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ec6a2d',
                    cancelButtonColor: '#d1d5db',
                    confirmButtonText: 'Yes, Submit',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'font-bold rounded-lg px-6 py-2.5',
                        cancelButton: 'font-bold rounded-lg px-6 py-2.5 text-gray-700'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            } else {
                this.reportValidity();
            }
        });

        // Success Message from Session
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#ec6a2d',
                confirmButtonText: 'Okay',
                customClass: {
                    confirmButton: 'font-bold rounded-lg px-6 py-2.5'
                }
            });
        @endif
    </script>

@endsection