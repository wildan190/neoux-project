@extends('layouts.app', [
    'title' => 'New Support Request',
    'breadcrumbs' => [
        ['name' => 'Support', 'url' => route('support.index')],
        ['name' => 'New Request', 'url' => null],
    ]
])

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">HELPDESK</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Submit a Support Request</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Describe your issue and attach a screenshot if helpful. We'll get back to you as soon as possible.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 px-5 py-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-red-700 dark:text-red-300 text-sm font-semibold">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center gap-2"><i data-feather="alert-circle" class="w-4 h-4 flex-shrink-0"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf

                {{-- Subject --}}
                <div>
                    <label for="subject" class="block text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                        placeholder="e.g. Cannot access my company dashboard"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all @error('subject') border-red-400 @enderror">
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6"
                        placeholder="Please describe your issue in detail. Include what you were trying to do, what happened, and any error messages you saw."
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                </div>

                {{-- Screenshot Upload --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">
                        Screenshot <span class="text-gray-400 font-medium">(Optional)</span>
                    </label>
                    <div id="dropzone"
                         class="relative border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-10 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50/30 dark:hover:bg-primary-900/10 transition-all group">
                        <input type="file" name="screenshot" id="screenshot" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div id="dropzone-placeholder">
                            <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/30 transition-colors">
                                <i data-feather="image" class="w-7 h-7 text-gray-400 group-hover:text-primary-500 transition-colors"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-600 dark:text-gray-300">Click or drag to upload a screenshot</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">PNG, JPG, GIF, WEBP — Max 5MB</p>
                        </div>
                        <div id="dropzone-preview" class="hidden">
                            <img id="preview-img" src="" alt="Preview" class="max-h-48 mx-auto rounded-xl shadow-md object-contain">
                            <p id="preview-name" class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-3"></p>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('support.index') }}"
                       class="px-5 py-2.5 text-gray-500 dark:text-gray-400 text-sm font-bold hover:text-gray-800 dark:hover:text-white transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex items-center gap-2 px-8 py-3 bg-primary-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:scale-105 transition-all">
                        <i data-feather="send" class="w-4 h-4"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') feather.replace();

        const input = document.getElementById('screenshot');
        const placeholder = document.getElementById('dropzone-placeholder');
        const preview = document.getElementById('dropzone-preview');
        const previewImg = document.getElementById('preview-img');
        const previewName = document.getElementById('preview-name');

        input.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    previewName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                    placeholder.classList.add('hidden');
                    preview.classList.remove('hidden');
                    if (typeof feather !== 'undefined') feather.replace();
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
