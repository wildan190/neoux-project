{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    {{-- Backdrop with Blur --}}
    <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true"
        onclick="closeImportModal()"></div>

    <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center sm:p-0">
        <div
            class="relative inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:w-full sm:max-w-4xl border border-gray-100 dark:border-gray-700">

            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2" id="modal-title">
                    <span
                        class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-lg text-primary-600 dark:text-primary-400">
                        <i data-feather="upload-cloud" class="w-5 h-5"></i>
                    </span>
                    Import Products
                </h3>
                <button onclick="closeImportModal()"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="px-6 py-6" id="step-1">
                {{-- Step 1: Upload --}}
                <div class="text-center space-y-4">
                    <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">
                        Bulk upload your products using the provided Excel template.
                        Ensure your file follows the correct format (`.xlsx`).
                    </p>

                    <form id="importForm" enctype="multipart/form-data">
                        @csrf
                        <div
                            class="mt-4 flex justify-center px-6 pt-10 pb-10 border-2 border-gray-300 border-dashed rounded-xl hover:border-primary-500 hover:bg-primary-50/10 transition-all cursor-pointer group relative">
                            <input id="file-upload" name="file" type="file"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                accept=".xlsx,.xls">
                            <div class="space-y-2 text-center pointer-events-none">
                                <div
                                    class="mx-auto w-16 h-16 bg-primary-100 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i data-feather="file-text" class="w-8 h-8"></i>
                                </div>
                                <div class="text-gray-600 dark:text-gray-300 font-medium">
                                    <span class="text-primary-600 dark:text-primary-400">Click to upload</span> or drag
                                    and drop
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">XLSX, XLS files only (max 10MB)</p>
                                <p id="filename-display"
                                    class="text-sm text-primary-600 dark:text-primary-400 mt-2 font-bold"></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="px-6 py-6 hidden" id="step-2">
                {{-- Step 2: Preview --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Data Preview</h4>
                        <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Showing first
                            5 rows</span>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="preview-table">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                {{-- Headers will be injected here --}}
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- Rows will be injected here --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Progress Bar --}}
                    <div id="import-progress" class="hidden mt-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-primary-700 dark:text-primary-300">Importing...</span>
                            <span class="text-sm font-medium text-primary-700 dark:text-primary-300"
                                id="progress-percent">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                            <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"
                                id="progress-bar"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeImportModal()"
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="previewImport()" id="btn-preview"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-600/20 transition-all">
                    Preview Data
                </button>
                <button type="button" onclick="submitImport()" id="btn-import"
                    class="hidden px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg shadow-green-600/20 transition-all">
                    Confirm & Import
                </button>
            </div>
        </div>
    </div>
</div>

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

    document.getElementById('file-upload').addEventListener('change', function (e) {
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
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

        // Body (Remaining rows)
        // Skip first row if it's header, but here we just show all for simplicity or slice
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
                ,
                    'Accept': 'application/json'
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

    async function checkStatus(jobId) {
        const interval = setInterval(async () => {
            try {
                const res = await fetch(`/catalogue/import/status/${jobId}`);
                const data = await res.json();

                if (data.status === 'completed') {
                    clearInterval(interval);
                    Swal.fire({
                        title: 'Success!',
                        text: 'Products imported successfully.',
                        icon: 'success',
                        confirmButtonText: 'Refresh Page'
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (data.status === 'failed') {
                    clearInterval(interval);
                    Swal.fire('Error', 'Import failed. Check logs for details.', 'error');
                    resetImportForm();
                } else {
                    // Update progress
                    if (data.total_rows > 0) {
                        const percent = Math.round((data.processed_rows / data.total_rows) * 100);
                        document.getElementById('progress-bar').style.width = percent + '%';
                        document.getElementById('progress-percent').innerText = percent + '%';
                    }
                }
            } catch (e) {
                console.error(e);
            }
        }, 2000);
    }
</script>