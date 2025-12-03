{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 hidden z-50 overflow-y-auto" aria-modal="true">

    <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center relative">

        <!-- Overlay (blur background) -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 transition-opacity" onclick="closeImportModal()">
        </div>

        <!-- Modal Panel -->
        <div id="importModalPanel" class="relative z-50 inline-block w-full max-w-lg overflow-hidden
                    bg-white dark:bg-gray-800 rounded-2xl shadow-xl border 
                    border-gray-200 dark:border-gray-700 transform transition-all
                    opacity-0 scale-95">

            <form action="{{ route('catalogue.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Header -->
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-start space-x-4">
                        <div
                            class="flex items-center justify-center w-12 h-12 bg-primary-100 dark:bg-primary-900/40 rounded-full">
                            <i data-feather="upload-cloud" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                        </div>

                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Import Products
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Upload products using our Excel/CSV template.
                            </p>
                        </div>
                    </div>

                    <!-- Template Box -->
                    <div
                        class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <i data-feather="file-text" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Import Template</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">CSV format</p>
                                </div>
                            </div>

                            <a href="{{ route('catalogue.import.template') }}" class="px-3 py-1.5 text-sm font-medium bg-white dark:bg-gray-700 
                                      border border-gray-200 dark:border-gray-600 rounded-lg 
                                      hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                Download
                            </a>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div id="upload-section" class="mt-6">
                        <div id="dropzone" class="px-6 py-8 text-center border-2 border-dashed border-gray-300 
                                    dark:border-gray-600 rounded-xl hover:border-primary-500 
                                    dark:hover:border-primary-400 transition cursor-pointer 
                                    bg-gray-50 dark:bg-gray-700/40">

                            <div class="mx-auto w-12 h-12 text-gray-400">
                                <i data-feather="upload" class="w-full h-full"></i>
                            </div>

                            <label for="file-upload"
                                class="block mt-3 text-sm font-medium text-primary-600 cursor-pointer">
                                Upload a file
                            </label>
                            <input id="file-upload" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv">

                            <p class="text-sm text-gray-500 dark:text-gray-400">or drag and drop</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">XLSX, XLS, CSV up to 10MB</p>

                            <p id="file-name-display"
                                class="mt-3 text-sm font-medium text-gray-900 dark:text-white hidden"></p>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div id="preview-section" class="mt-6 hidden">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Data Preview</h4>
                            <span id="preview-row-count" class="text-xs text-gray-500 dark:text-gray-400"></span>
                        </div>

                        <div class="max-h-96 overflow-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead id="preview-thead" class="bg-gray-50 dark:bg-gray-700 sticky top-0"></thead>
                                <tbody id="preview-tbody"
                                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                </tbody>
                            </table>
                        </div>

                        <button type="button" onclick="resetUpload()"
                            class="mt-4 text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                            ← Upload Different File
                        </button>
                    </div>

                    <!-- Loading -->
                    <div id="preview-loading" class="hidden mt-6 text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Loading preview...</p>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-700/40 border-t border-gray-200 dark:border-gray-600 flex justify-end space-x-3">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-sm font-medium bg-white dark:bg-gray-800 
                                   border border-gray-300 dark:border-gray-600 rounded-lg 
                                   hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>

                    <button id="btn-start-import" type="submit" class="hidden px-4 py-2 text-sm font-semibold text-white bg-primary-600 
                                   rounded-lg hover:bg-primary-700 transition">
                        Confirm & Start Import
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    let currentFile = null;

    function openImportModal() {
        const modal = document.getElementById('importModal');
        const panel = document.getElementById('importModalPanel');

        modal.classList.remove('hidden');
        setTimeout(() => {
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        if (typeof feather !== 'undefined') feather.replace();
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        const panel = document.getElementById('importModalPanel');

        panel.classList.add('opacity-0', 'scale-95');
        panel.classList.remove('opacity-100', 'scale-100');

        setTimeout(() => {
            modal.classList.add('hidden');
            resetUpload();
        }, 150);
    }

    function resetUpload() {
        currentFile = null;
        document.getElementById('file-upload').value = '';
        document.getElementById('file-name-display').classList.add('hidden');
        document.getElementById('upload-section').classList.remove('hidden');
        document.getElementById('preview-section').classList.add('hidden');
        document.getElementById('preview-loading').classList.add('hidden');
        document.getElementById('btn-start-import').classList.add('hidden');
    }

    function loadPreview(file) {
        currentFile = file;
        document.getElementById('upload-section').classList.add('hidden');
        document.getElementById('preview-loading').classList.remove('hidden');

        const formData = new FormData();
        formData.append('file', file);

        fetch('{{ route('catalogue.import.preview') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData,
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('preview-loading').classList.add('hidden');

                if (data.success) {
                    renderPreview(data);
                    document.getElementById('preview-section').classList.remove('hidden');
                    document.getElementById('btn-start-import').classList.remove('hidden');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Preview Failed',
                        text: data.error || 'Failed to load preview',
                        confirmButtonColor: '#4F46E5'
                    });
                    resetUpload();
                }
            })
            .catch(error => {
                document.getElementById('preview-loading').classList.add('hidden');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load preview: ' + error.message,
                    confirmButtonColor: '#4F46E5'
                });
                resetUpload();
            });
    }

    function renderPreview(data) {
        const thead = document.getElementById('preview-thead');
        const tbody = document.getElementById('preview-tbody');
        const rowCount = document.getElementById('preview-row-count');

        rowCount.textContent = `Showing ${data.total_rows} of ${data.total_rows} rows (first 20)`;

        // Render headers
        let headerHtml = '<tr>';
        data.headers.forEach(header => {
            headerHtml += `<th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">${header}</th>`;
        });
        headerHtml += '</tr>';
        thead.innerHTML = headerHtml;

        // Render rows
        let rowsHtml = '';
        data.data.forEach(row => {
            rowsHtml += '<tr>';
            data.headers.forEach(header => {
                const value = row[header] || '';
                rowsHtml += `<td class="px-4 py-3 text-xs text-gray-900 dark:text-gray-300">${value}</td>`;
            });
            rowsHtml += '</tr>';
        });
        tbody.innerHTML = rowsHtml;
    }

    // File input handlers
    const fileInput = document.getElementById('file-upload');
    const dropzone = document.getElementById('dropzone');
    const fileDisplay = document.getElementById('file-name-display');

    fileInput.addEventListener('change', function (e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            fileDisplay.textContent = '📄 ' + file.name;
            fileDisplay.classList.remove('hidden');
            loadPreview(file);
        }
    });

    dropzone.addEventListener('click', (e) => {
        if (e.target.id !== 'file-upload') {
            fileInput.click();
        }
    });

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('border-primary-500', 'dark:border-primary-400', 'bg-primary-50', 'dark:bg-primary-900/20');
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('border-primary-500', 'dark:border-primary-400', 'bg-primary-50', 'dark:bg-primary-900/20');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('border-primary-500', 'dark:border-primary-400', 'bg-primary-50', 'dark:bg-primary-900/20');

        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            const file = files[0];
            const validTypes = ['.xlsx', '.xls', '.csv'];
            const isValid = validTypes.some(type => file.name.toLowerCase().endsWith(type));

            if (isValid) {
                fileInput.files = files;
                fileDisplay.textContent = '📄 ' + file.name;
                fileDisplay.classList.remove('hidden');
                loadPreview(file);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload a valid Excel or CSV file (.xlsx, .xls, .csv)',
                    confirmButtonColor: '#4F46E5'
                });
            }
        }
    });
</script>