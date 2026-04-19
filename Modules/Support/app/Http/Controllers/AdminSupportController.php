<?php

namespace Modules\Support\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Support\Models\SupportTicket;

class AdminSupportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = SupportTicket::with('user')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $tickets = $query->paginate(20);

        $counts = [
            'all'         => SupportTicket::count(),
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved'    => SupportTicket::where('status', 'resolved')->count(),
        ];

        return view('support::admin.index', compact('tickets', 'status', 'counts'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('user');
        return view('support::admin.show', compact('ticket'));
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status'      => 'required|in:open,in_progress,resolved',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $ticket->update($validated);

        return redirect()->route('admin.support.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }
}
