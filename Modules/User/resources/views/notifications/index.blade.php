@extends('layouts.app', [
    'title' => 'Network Notifications',
    'breadcrumbs' => [
        ['name' => 'Account', 'url' => url('/')],
        ['name' => 'Notifications', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">NETWORK LOG</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Events: {{ $notifications->total() }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                System <span class="text-primary-600">Notifications</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                @csrf
                <button type="submit" class="h-14 px-8 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[10px] font-black text-gray-400 uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                    Synchronize All As Read
                </button>
            </form>
        </div>
    </div>

    {{-- Notification List --}}
    <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @forelse($notifications as $notification)
                <div class="p-8 md:p-10 group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-all @if(!$notification->read_at) border-l-4 border-primary-600 @endif relative">
                    <div class="flex flex-col md:flex-row md:items-center gap-8">
                        {{-- Icon node --}}
                        <div class="w-16 h-16 rounded-[1.5rem] {{ $notification->read_at ? 'bg-gray-50 dark:bg-gray-900' : 'bg-primary-50 dark:bg-primary-900/20' }} flex items-center justify-center relative overflow-hidden shrink-0 transition-all group-hover:scale-105">
                            @php
                                $type = $notification->data['type'] ?? 'info';
                                $icon = match($type) {
                                    'pr_status' => 'file-text',
                                    'invoice' => 'dollar-sign',
                                    'po' => 'package',
                                    'return' => 'refresh-ccw',
                                    default => 'bell',
                                };
                                $colorClass = $notification->read_at ? 'text-gray-300' : 'text-primary-600';
                            @endphp
                            <i data-feather="{{ $icon }}" class="w-7 h-7 {{ $colorClass }}"></i>
                            <div class="absolute inset-0 bg-white/10 dark:bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight leading-tight @if(!$notification->read_at) group-hover:text-primary-600 @endif transition-colors">
                                    {{ $notification->data['title'] ?? 'Network Event Detected' }}
                                </h4>
                                <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest tabular-nums">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-loose mb-6">
                                {{ $notification->data['message'] ?? 'Platform operation protocol executed without detailed description.' }}
                            </p>
                            
                            <div class="flex items-center gap-6">
                                <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[9px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.2em] flex items-center gap-2 group/btn">
                                        Executive Action <i data-feather="arrow-right" class="w-3 h-3 group-hover/btn:translate-x-1 transition-transform"></i>
                                    </button>
                                </form>
                                @if(!$notification->read_at)
                                    <div class="w-1.5 h-1.5 rounded-full bg-primary-600 animate-ping"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-24 text-center">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-gray-200">
                        <i data-feather="inbox" class="w-10 h-10"></i>
                    </div>
                    <p class="text-[11px] font-black text-gray-300 uppercase tracking-[0.2em]">Zero network events in log</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-10 py-8 border-t border-gray-50 dark:border-gray-800">
                {{ $notifications->links() }}
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
