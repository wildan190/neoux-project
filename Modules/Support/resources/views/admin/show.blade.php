@extends('layouts.app', ['title' => 'Ticket #' . $ticket->id])

@section('content')
@php
    $colors = ['open' => 'amber', 'in_progress' => 'blue', 'resolved' => 'emerald'];
    $c = $colors[$ticket->status] ?? 'gray';
@endphp
<div class="p-8 max-w-3xl mx-auto">

    {{-- Back --}}
    <a href="{{ route('admin.support.index') }}"
       class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-[11px] font-black uppercase tracking-widest mb-8 transition-colors">
        <i data-feather="arrow-left" class="w-4 h-4"></i> Back to Tickets
    </a>

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <span class="px-2.5 py-1 bg-{{ $c }}-900/40 text-{{ $c }}-400 text-[9px] font-black uppercase tracking-widest rounded-lg">{{ $ticket->status_label }}</span>
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Ticket #{{ $ticket->id }}</span>
        </div>
        <h1 class="text-2xl font-black text-white tracking-tight">{{ $ticket->subject }}</h1>
        <p class="text-xs text-gray-500 mt-1">Submitted by <strong class="text-gray-300">{{ $ticket->user->name }}</strong> ({{ $ticket->user->email }}) · {{ $ticket->created_at->format('d M Y, H:i') }}</p>
    </div>

    {{-- Description --}}
    <div class="bg-gray-800/50 rounded-[2rem] border border-gray-700/50 p-8 mb-6">
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">User's Description</p>
        <p class="text-sm text-gray-300 whitespace-pre-line leading-relaxed">{{ $ticket->description }}</p>

        @if($ticket->screenshot_path)
            <div class="mt-8">
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">Attached Screenshot</p>
                <a href="{{ Storage::url($ticket->screenshot_path) }}" target="_blank">
                    <img src="{{ Storage::url($ticket->screenshot_path) }}"
                         alt="Screenshot"
                         class="rounded-2xl border border-gray-700 shadow-xl max-h-96 object-contain hover:opacity-90 transition-opacity cursor-zoom-in">
                </a>
            </div>
        @endif
    </div>

    {{-- Resolve Form --}}
    <div class="bg-gray-800/50 rounded-[2rem] border border-gray-700/50 p-8">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Update Ticket</p>

        @if(session('success'))
            <div class="mb-5 px-4 py-3 bg-emerald-900/30 border border-emerald-700 rounded-xl text-emerald-400 text-sm font-semibold flex items-center gap-2">
                <i data-feather="check-circle" class="w-4 h-4"></i>{{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.support.update', $ticket) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            {{-- Status --}}
            <div>
                <label for="status" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-sm font-bold text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>

            {{-- Admin Notes --}}
            <div>
                <label for="admin_notes" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                    Response / Resolution Notes
                </label>
                <textarea name="admin_notes" id="admin_notes" rows="6"
                    placeholder="Write your response or resolution notes here. This will be visible to the user."
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-sm font-medium text-white placeholder-gray-600 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all resize-none">{{ old('admin_notes', $ticket->admin_notes) }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="flex items-center gap-2 px-8 py-3 bg-primary-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:scale-105 transition-all">
                    <i data-feather="save" class="w-4 h-4"></i>
                    Save & Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>document.addEventListener('DOMContentLoaded',()=>{ if(typeof feather!=='undefined') feather.replace(); });</script>
@endpush
