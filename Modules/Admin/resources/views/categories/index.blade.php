@extends('layouts.app', [
    'title' => 'Category Architecture',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Catalogue Management', 'url' => null],
        ['name' => 'Categories', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12 pb-24">
    @if(session('success'))
        <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-[2rem] flex items-center gap-4 text-emerald-600 text-[11px] font-black uppercase tracking-widest animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                <i data-feather="check" class="w-4 h-4"></i>
            </div>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-6 bg-red-50 border border-red-100 rounded-[2rem] space-y-2 animate-in fade-in slide-in-from-top-4 duration-500">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-4 text-red-600 text-[11px] font-black uppercase tracking-widest">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <i data-feather="alert-circle" class="w-4 h-4"></i>
                    </div>
                    {{ $error }}
                </div>
            @endforeach
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">TAXONOMY CORE</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global Categories: {{ $categories->count() }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                Marketplace <span class="text-primary-600">Taxonomy Architecture</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <button onclick="document.getElementById('category_modal').classList.remove('hidden')" class="h-16 px-10 flex items-center bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-900/20 hover:bg-black transition-all active:scale-[0.98]">
                Deploy Category Node
            </button>
        </div>
    </div>

    {{-- Category Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($categories as $category)
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm group hover:border-primary-500 transition-all duration-500 relative overflow-hidden flex flex-col h-full">
                <div class="absolute top-0 right-0 -mr-12 -mt-12 w-32 h-32 bg-primary-50 dark:bg-primary-900/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                
                <div class="flex items-start justify-between mb-8 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-3 relative overflow-hidden shadow-inner group-hover:scale-105 transition-transform duration-500">
                        @if($category->icon)
                            <img src="{{ asset('storage/' . $category->icon) }}" class="w-full h-full object-contain">
                        @else
                            <i data-feather="folder" class="w-6 h-6 text-gray-300"></i>
                        @endif
                    </div>
                </div>

                <div class="mb-10 flex-1 relative z-10">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 group-hover:text-primary-600 transition-colors">{{ $category->name }}</h3>
                    <p class="text-[9px] font-black text-primary-600 uppercase tracking-widest mb-4">Internal ID: #{{ $category->id }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed line-clamp-2">
                        {{ $category->description ?: 'NO ARCHITECTURAL DESCRIPTION PROVIDED FOR THIS TAXONOMY NODE.' }}
                    </p>
                </div>

                <div class="pt-8 border-t border-gray-50 dark:border-gray-700 mt-auto relative z-10 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 leading-none">Products linked</span>
                        <span class="text-xs font-black text-gray-900 dark:text-white tabular-nums">{{ $category->products_count ?? 0 }} Assets</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="editCategory({{ json_encode($category) }})" class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:bg-white hover:text-primary-600 hover:shadow-sm transition-all">
                            <i data-feather="edit-2" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('CRITICAL: Permanent removal of taxonomy node?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                <i data-feather="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center bg-gray-50/50 dark:bg-gray-900/30 rounded-[3rem] border border-dashed border-gray-100 dark:border-gray-800">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Zero taxonomy nodes established</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Category Modal --}}
<div id="category_modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6 bg-gray-900/80 backdrop-blur-sm">
    <div class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-[3.5rem] p-12 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-12 text-gray-50/50 dark:text-gray-900/50 pointer-events-none">
            <i data-feather="folder-plus" class="w-32 h-32"></i>
        </div>
        
        <h3 id="modal_title" class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 relative z-10">Deploy Category Node</h3>
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-10 relative z-10">Configure Marketplace Taxonomy Asset</p>

        <form id="category_form" action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 relative z-10">
            @csrf
            <div id="method_field"></div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Category Identity</label>
                    <input type="text" name="name" id="cat_name" required 
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-5 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 shadow-inner @error('name') ring-2 ring-red-500 @enderror">
                    @error('name') <p class="mt-2 text-[9px] font-black text-red-500 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Architectural Icon</label>
                    <input type="file" name="icon" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-5 text-[10px] font-black uppercase tracking-tight focus:ring-primary-500 shadow-inner @error('icon') ring-2 ring-red-500 @enderror">
                    @error('icon') <p class="mt-2 text-[9px] font-black text-red-500 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Functional Description</label>
                    <textarea name="description" id="cat_description" rows="3"
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-6 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 shadow-inner leading-relaxed @error('description') ring-2 ring-red-500 @enderror"></textarea>
                    @error('description') <p class="mt-2 text-[9px] font-black text-red-500 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6">
                <button type="button" onclick="document.getElementById('category_modal').classList.add('hidden')" class="h-16 px-10 text-[11px] font-black text-gray-400 uppercase tracking-widest">Discard</button>
                <button type="submit" class="h-16 flex-1 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-900/20 hover:bg-black transition-all">Submit Protocol</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editCategory(category) {
        const modal = document.getElementById('category_modal');
        const form = document.getElementById('category_form');
        const title = document.getElementById('modal_title');
        const methodField = document.getElementById('method_field');
        
        title.innerText = 'Refine Category Node';
        form.action = `/admin/categories/${category.id}`;
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('cat_name').value = category.name;
        document.getElementById('cat_description').value = category.description || '';
        
        modal.classList.remove('hidden');
    }

    // Reset modal on manual open
    window.addEventListener('click', function(e) {
        if (e.target.innerText === 'Deploy Category Node') {
            const form = document.getElementById('category_form');
            const title = document.getElementById('modal_title');
            const methodField = document.getElementById('method_field');
            
            title.innerText = 'Deploy Category Node';
            form.action = "{{ route('admin.categories.store') }}";
            methodField.innerHTML = '';
            form.reset();
        }
    });
</script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();

        @if($errors->any())
            document.getElementById('category_modal').classList.remove('hidden');
        @endif
    });
</script>
@endpush
