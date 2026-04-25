{{-- Import History Modal --}}
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-md transition-opacity z-0" aria-hidden="true" onclick="closeImportModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block relative z-10 align-middle bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-gray-700">
            {{-- Step 1: Upload --}}
            <div id="importStepUpload">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 sm:mx-0 sm:h-10 sm:w-10 text-center">
                                <i data-feather="upload-cloud" class="w-6 h-6"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">
                                    Import PO History
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Upload an Excel file (.xlsx) containing your historical Purchase Orders.
                                    </p>
                                </div>
                                <input type="hidden" name="import_role" value="{{ session('procurement_mode', 'buyer') }}">
                                <div class="mt-6">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Select File</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-xl hover:border-primary-500 dark:hover:border-primary-500 transition-colors bg-gray-50 dark:bg-gray-700/50">
                                        <div class="space-y-1 text-center">
                                            <i data-feather="file" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-2"></i>
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-bold text-primary-600 dark:text-primary-400 hover:text-primary-500 focus-within:outline-none px-2 py-0.5">
                                                    <span>Upload a file</span>
                                                    <input id="file-upload" name="file" type="file" class="sr-only" required accept=".xlsx,.xls,.csv">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="file-name-display" class="mt-2 text-sm text-primary-600 dark:text-primary-400 font-bold text-center"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit" id="btnPreview" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none transition shadow-lg shadow-primary-500/30 disabled:opacity-50">
                            <span id="previewText">Analyze File</span>
                            <span id="previewLoading" class="hidden animate-spin ml-2">
                                <i data-feather="loader" class="w-4 h-4"></i>
                            </span>
                        </button>
                        <button type="button" onclick="closeImportModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors uppercase tracking-widest">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- Step 2: Preview --}}
            <div id="importStepPreview" class="hidden">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white mb-4">
                        Import Preview
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Showing first 20 rows of <span id="totalRowsCount" class="font-bold"></span> total records found.
                    </p>
                    
                    <div class="overflow-x-auto max-h-[400px] border border-gray-100 dark:border-gray-700 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead id="previewHeader" class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                                {{-- Headers will be injected --}}
                            </thead>
                            <tbody id="previewBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                {{-- Rows will be injected --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <form action="{{ route('procurement.po.confirm-import') }}" method="POST">
                    @csrf
                    <input type="hidden" name="temp_path" id="tempPathInput">
                    <input type="hidden" name="import_role" id="importRoleInput">
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none transition shadow-lg shadow-emerald-500/30">
                            Confirm & Start Import
                        </button>
                        <button type="button" onclick="backToUpload()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-colors uppercase tracking-widest">
                            Change File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
