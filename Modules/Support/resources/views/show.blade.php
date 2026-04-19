@extends('layouts.app', [
    'title' => 'Support Ticket #' . $ticket->id,
    'breadcrumbs' => [
        ['name' => 'Support', 'url' => route('support.index')],
        ['name' => '#' . $ticket->id, 'url' => null],
    ]
])

@section('content')
    @php
        $colors = ['open' => 'amber', 'in_progress' => 'blue', 'resolved' => 'emerald'];
        $c = $colors[$ticket->status] ?? 'gray';
    @endphp
    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-2.5 py-1 bg-{{ $c }}-100 dark:bg-{{ $c }}-900/30 text-{{ $c }}-700 dark:text-{{ $c }}-400 text-[9px] font-black uppercase tracking-widest rounded-lg">
                        {{ $ticket->status_label }}
                    </span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ticket #{{ $ticket->id }}</span>
                </div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $ticket->subject }}</h1>
                <p class="text-xs text-gray-400 mt-1">Submitted on {{ $ticket->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        {{-- Your Request --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 p-8 shadow-sm space-y-6">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Your Description</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $ticket->description }}</p>
            </div>

            @if($ticket->screenshot_path)
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Attached Screenshot</p>
                    <a href="{{ Storage::url($ticket->screenshot_path) }}" target="_blank">
                        <img src="{{ Storage::url($ticket->screenshot_path) }}"
                             alt="Screenshot"
                             class="rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md max-h-80 object-contain hover:shadow-xl transition-shadow cursor-zoom-in">
                    </a>
                </div>
            @endif
        </div>

        {{-- Admin Response --}}
        @if($ticket->admin_notes)
            <div class="bg-{{ $c }}-50 dark:bg-{{ $c }}-900/20 rounded-[2rem] border border-{{ $c }}-100 dark:border-{{ $c }}-800/30 p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-{{ $c }}-600 flex items-center justify-center">
                        <i data-feather="message-circle" class="w-4 h-4 text-white"></i>
                    </div>
                    <p class="text-[10px] font-black text-{{ $c }}-600 dark:text-{{ $c }}-400 uppercase tracking-widest">Response from Support Team</p>
                </div>
                <p class="text-sm text-{{ $c }}-900 dark:text-{{ $c }}-100 whitespace-pre-line leading-relaxed">{{ $ticket->admin_notes }}</p>
            </div>
        @else
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-[2rem] border border-gray-100 dark:border-gray-700 p-8 text-center">
                <i data-feather="clock" class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-3"></i>
                <p class="text-sm font-bold text-gray-400 dark:text-gray-500">Awaiting response from our support team.</p>
                <p class="text-[10px] font-bold text-gray-300 dark:text-gray-600 mt-1 uppercase tracking-widest">We typically respond within 1–2 business days.</p>
            </div>
        @endif

        <div class="flex justify-start">
            <a href="{{ route('support.index') }}"
               class="flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
                Back to My Tickets
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>document.addEventListener('DOMContentLoaded', () => { if (typeof feather !== 'undefined') feather.replace(); });</script>
@endpush
