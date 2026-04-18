@extends('layouts.app', [
    'title' => 'System Administrators',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Admin Nodes', 'url' => null],
    ]
])

@section('content')
<div class="space-y-10">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">PRIVILEGED ACCESS</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global Controllers: {{ $admins->total() }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                System <span class="text-primary-600">Administrative Nodes</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.admins.create') }}" class="h-16 px-10 flex items-center bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-gray-900/10 hover:bg-black transition-all active:scale-[0.98]">
                Deploy Admin Node
            </a>
        </div>
    </div>

    {{-- Admin Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800">Admin Identity</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800">Access Level</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800">Uptime</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($admins as $admin)
                        <tr class="group hover:bg-gray-50/30 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-900 flex items-center justify-center text-primary-500 shadow-xl group-hover:scale-105 transition-transform duration-500">
                                        <i data-feather="terminal" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2 group-hover:text-primary-600 transition-colors">
                                            {{ $admin->name }}
                                        </p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <span class="px-3 py-1 bg-red-100 text-red-600 text-[9px] font-black rounded-lg uppercase tracking-widest">System Controller</span>
                            </td>
                            <td class="px-10 py-8 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                {{ $admin->created_at->diffForHumans() }}
                            </td>
                            <td class="px-10 py-8 text-right">
                                @if(auth('admin')->id() !== $admin->id)
                                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('CRITICAL: Terminate administrative access for this node?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                                            <i data-feather="slash" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Local Session</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 py-24 text-center">
                                <p class="text-[11px] font-black text-gray-300 uppercase tracking-[0.2em]">Zero administrative nodes detected</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admins->hasPages())
            <div class="px-10 py-8 border-t border-gray-50 dark:border-gray-800">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
