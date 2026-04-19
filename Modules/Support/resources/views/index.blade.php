@extends('layouts.app', [
    'title' => 'My Support Tickets',
    'breadcrumbs' => [
        ['name' => 'Bantuan / Support', 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">HELPDESK</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $tickets->total() }} Tickets</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Support Requests</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track the status of your help requests.</p>
        </div>
        <a href="{{ route('support.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:scale-105 transition-all">
            <i data-feather="plus" class="w-4 h-4"></i>
            New Request
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 text-sm font-semibold flex items-center gap-3">
            <i data-feather="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($tickets->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-feather="life-buoy" class="w-10 h-10 text-gray-200"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">No Support Requests</h3>
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest px-12 leading-relaxed">
                Having trouble? Reach out to our team and we'll help you get back on track.
            </p>
            <a href="{{ route('support.create') }}"
               class="mt-8 inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:scale-105 transition-all">
                <i data-feather="plus" class="w-4 h-4"></i>
                Submit a Request
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($tickets as $ticket)
                @php
                    $colors = ['open' => 'amber', 'in_progress' => 'blue', 'resolved' => 'emerald'];
                    $c = $colors[$ticket->status] ?? 'gray';
                @endphp
                <a href="{{ route('support.show', $ticket) }}"
                   class="block bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm hover:shadow-lg hover:shadow-gray-200/50 dark:hover:shadow-none transition-all group">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-2.5 py-1 bg-{{ $c }}-100 dark:bg-{{ $c }}-900/30 text-{{ $c }}-700 dark:text-{{ $c }}-400 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                    {{ $ticket->status_label }}
                                </span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">#{{ $ticket->id }}</span>
                            </div>
                            <h3 class="text-base font-black text-gray-900 dark:text-white truncate group-hover:text-primary-600 transition-colors">{{ $ticket->subject }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">{{ $ticket->description }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $ticket->created_at->format('d M Y') }}</p>
                            <p class="text-[10px] font-bold text-gray-300 dark:text-gray-600">{{ $ticket->created_at->format('H:i') }}</p>
                            <i data-feather="chevron-right" class="w-4 h-4 text-gray-300 dark:text-gray-600 mt-1 group-hover:text-primary-500 transition-colors ml-auto"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($tickets->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $tickets->links() }}
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    <script>document.addEventListener('DOMContentLoaded', () => { if (typeof feather !== 'undefined') feather.replace(); });</script>
@endpush
