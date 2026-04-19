<?php

namespace Modules\Support\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Support\Models\SupportTicket;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('support::index', compact('tickets'));
    }

    public function create()
    {
        return view('support::create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'screenshot'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('support/screenshots', 'public');
        }

        SupportTicket::create([
            'user_id'         => Auth::id(),
            'subject'         => $validated['subject'],
            'description'     => $validated['description'],
            'screenshot_path' => $screenshotPath,
            'status'          => 'open',
        ]);

        return redirect()->route('support.index')
            ->with('success', 'Your support request has been submitted. Our team will respond shortly.');
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        return view('support::show', compact('ticket'));
    }
}
