@extends('layouts.app', [
    'title' => 'Create Company',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Companies', 'url' => route('companies.index')],
        ['name' => 'Create', 'url' => '#']
    ]
])

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
    <div class="p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Register New Company</h2>
            
            <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                {{-- Logo Upload --}}
                <div class="flex items-center gap-6">
                    <div class="shrink-0">
                        <div id="logo-preview" class="h-24 w-24 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <i data-feather="image" class="w-8 h-8 text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Logo</label>
                        <div class="mt-1 flex items-center gap-3">
                            <label for="logo" class="cursor-pointer bg-white dark:bg-gray-700 py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <span>Change</span>
                                <input id="logo" name="logo" type="file" class="sr-only" accept="image/*" onchange="previewLogo(this)">
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, GIF up to 2MB</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Basic Info --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>

                    <div>
                        <label for="business_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Category</label>
                        <input type="text" name="business_category" id="business_category" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select name="category" id="category" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="buyer">Buyer</option>
                            <option value="supplier">Supplier</option>
                            <option value="vendor">Vendor</option>
                        </select>
                    </div>

                    <div>
                        <label for="npwp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NPWP</label>
                        <input type="text" name="npwp" id="npwp" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="email" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" id="phone" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                        <input type="url" name="website" id="website" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                        <input type="text" name="country" id="country" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="tag" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tags</label>
                        <input type="text" name="tag" id="tag" placeholder="e.g. Technology, Retail" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    </div>
                </div>

                {{-- Operation Locations --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Operation Locations</label>
                        <button type="button" onclick="addLocation()" class="text-sm text-primary-600 hover:text-primary-500 font-medium">+ Add Location</button>
                    </div>
                    <div id="locations-container" class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" name="locations[]" class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter location address">
                        </div>
                    </div>
                </div>

                {{-- Drag & Drop Documents --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Documents</label>
                    <div id="drop-zone" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-primary-500 transition-colors cursor-pointer bg-gray-50 dark:bg-gray-700/50">
                        <input type="file" name="documents[]" id="documents" multiple class="hidden">
                        <i data-feather="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PDF, JPG, PNG up to 2MB</p>
                    </div>
                    <div id="file-list" class="mt-4 space-y-2"></div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2.5 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 font-medium shadow-lg shadow-primary-500/30 transition-all">
                        Create Company
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const dt = new DataTransfer();
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('documents');
    const fileList = document.getElementById('file-list');

    function previewLogo(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo-preview').innerHTML = '<img src="'+e.target.result+'" class="h-full w-full object-cover">';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addLocation() {
        const container = document.getElementById('locations-container');
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
            <input type="text" name="locations[]" class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter location address">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 p-2">
                <i data-feather="trash-2" class="w-5 h-5"></i>
            </button>
        `;
        container.appendChild(div);
        feather.replace();
    }

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        handleFiles(e.dataTransfer.files);
    });

    // Handle file input change (when clicking to upload)
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            // Check if file already exists
            let exists = false;
            for (let i = 0; i < dt.items.length; i++) {
                if (dt.items[i].getAsFile().name === file.name && dt.items[i].getAsFile().size === file.size) {
                    exists = true;
                    break;
                }
            }
            if (!exists) {
                dt.items.add(file);
            }
        });
        
        updateFileInput();
        renderFileList();
    }

    function updateFileInput() {
        fileInput.files = dt.files;
    }

    function removeFile(index) {
        dt.items.remove(index);
        updateFileInput();
        renderFileList();
    }

    function renderFileList() {
        fileList.innerHTML = '';
        Array.from(dt.files).forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600';
            div.innerHTML = `
                <div class="flex items-center gap-3">
                    <i data-feather="file" class="w-5 h-5 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">${file.name}</span>
                    <span class="text-xs text-gray-500">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                </div>
                <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700 p-1">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            `;
            fileList.appendChild(div);
        });
        feather.replace();
    }
</script>
@endsection
