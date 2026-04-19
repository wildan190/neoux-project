@extends('layouts.app', ['title' => 'Support Tickets'])

@section('content')
<div class="p-8">
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">HELPDESK</p>
            <h1 class="text-2xl font-black text-white uppercase tracking-tight">Support Tickets</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 bg-amber-600/20 text-amber-400 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $counts['open'] }} Open</span>
            <span class="px-3 py-1.5 bg-blue-600/20 text-blue-400 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $counts['in_progress'] }} In Progress</span>
            <span class="px-3 py-1.5 bg-emerald-600/20 text-emerald-400 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $counts['resolved'] }} Resolved</span>
        </div>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="flex items-center gap-2 mb-6 bg-gray-800/50 rounded-2xl p-1.5 w-fit">
        @foreach(['all' => 'All', 'open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved'] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}"
               class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all {{ $status === $key ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-400 hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-emerald-900/30 border border-emerald-700 rounded-2xl text-emerald-400 text-sm font-semibold flex items-center gap-3">
            <i data-feather="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($tickets->isEmpty())
        <div class="bg-gray-800/50 rounded-[2rem] border border-gray-700/50 p-16 text-center">
            <i data-feather="inbox" class="w-12 h-12 text-gray-600 mx-auto mb-4"></i>
            <p class="text-gray-400 font-bold">No tickets found for this filter.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($tickets as $ticket)
                @php
                    $colors = ['open' => 'amber', 'in_progress' => 'blue', 'resolved' => 'emerald'];
                    $c = $colors[$ticket->status] ?? 'gray';
                @endphp
                <a href="{{ route('admin.support.show', $ticket) }}"
                   class="flex items-center gap-6 bg-gray-800/50 hover:bg-gray-800 border border-gray-700/50 hover:border-gray-600 rounded-2xl px-6 py-5 transition-all group">
                    <div class="flex-shrink-0">
                        <span class="px-2.5 py-1 bg-{{ $c }}-900/40 text-{{ $c }}-400 text-[9px] font-black uppercase tracking-widest rounded-lg">{{ $ticket->status_label }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">#{{ $ticket->id }} · {{ $ticket->user->name ?? 'Unknown' }} · {{ $ticket->user->email ?? '' }}</p>
                        <h3 class="text-sm font-black text-white truncate group-hover:text-primary-400 transition-colors">{{ $ticket->subject }}</h3>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ $ticket->description }}</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-[10px] font-bold text-gray-500">{{ $ticket->created_at->format('d M Y') }}</p>
                        <p class="text-[10px] text-gray-600">{{ $ticket->created_at->diffForHumans() }}</p>
                        @if($ticket->screenshot_path)
                            <span class="inline-flex items-center gap-1 mt-1 text-[9px] font-bold text-gray-600 uppercase tracking-widest">
                                <i data-feather="image" class="w-3 h-3"></i> Screenshot
                            </span>
                        @endif
                    </div>
                    <i data-feather="chevron-right" class="w-4 h-4 text-gray-600 group-hover:text-primary-400 transition-colors flex-shrink-0"></i>
                </a>
            @endforeach
        </div>

        @if($tickets->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $tickets->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
    <script>document.addEventListener('DOMContentLoaded',()=>{ if(typeof feather!=='undefined') feather.replace(); });</script>
@endpush
