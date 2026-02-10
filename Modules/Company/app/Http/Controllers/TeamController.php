<?php

namespace Modules\Company\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\TeamInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Company\Models\Company;
use Modules\Company\Models\CompanyInvitation;
use Modules\User\Models\User;

class TeamController extends Controller
{
    /**
     * Display team members
     */
    public function index()
    {
        $companyId = session('selected_company_id');
        $company = Company::findOrFail($companyId);

        // Authorization: Check if user belongs to company
        if (! $company->members()->where('user_id', Auth::id())->exists() && $company->user_id !== Auth::id()) {
            abort(403);
        }

        $members = $company->members()->paginate(10);
        $invitations = $company->invitations()->where('status', 'pending')->get();

        return view('company.team.index', compact('company', 'members', 'invitations'));
    }

    /**
     * Invite a new member
     */
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,manager,buyer,staff,approver,purchasing_manager,finance',
        ]);

        $companyId = session('selected_company_id');
        $company = Company::findOrFail($companyId);

        // Check if user is already a member
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $company->members()->where('user_id', $existingUser->id)->exists()) {
            return back()->withErrors(['email' => 'User is already a member of this company.']);
        }

        // Create Invitation
        $invitation = CompanyInvitation::create([
            'company_id' => $company->id,
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::random(32),
            'status' => 'pending',
        ]);

        // Send Email
        Mail::to($request->email)->send(new TeamInvitationMail($invitation));

        return back()->with('success', 'Invitation sent successfully to '.$request->email);
    }

    /**
     * Accept invitation (GET)
     */
    public function acceptInvitation($token)
    {
        $invitation = CompanyInvitation::where('token', $token)->where('status', 'pending')->firstOrFail();

        // 1. If user is logged in
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->email !== $invitation->email) {
                return redirect()->route('dashboard')->with('error', 'This invitation was sent to a different email address ('.$invitation->email.').');
            }

            // Accept logic for logged in user
            $this->joinCompany($user, $invitation);

            return redirect()->route('dashboard')->with('success', 'You have joined '.$invitation->company->name);
        }

        // 2. If user is NOT logged in
        // Check if user already exists
        $userExists = User::where('email', $invitation->email)->exists();

        if ($userExists) {
            // Redirect to login if account exists
            return redirect()->route('login')->with('info', 'Please login to accept the invitation for '.$invitation->company->name);
        }

        // 3. User does not exist -> Show Register/Accept View
        return view('company.invitation.accept', compact('invitation'));
    }

    /**
     * Process invitation acceptance for NEW users (POST)
     */
    public function processAcceptInvitation(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $invitation = CompanyInvitation::where('token', $request->token)->where('status', 'pending')->firstOrFail();

        // Double check if user exists to prevent duplicate
        if (User::where('email', $invitation->email)->exists()) {
            return redirect()->route('login')->with('error', 'Account already exists. Please login.');
        }

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => bcrypt($request->password),
            'email_verified_at' => now(), // Auto verify since they clicked the email link
        ]);

        // Login User
        Auth::login($user);

        // Join Company
        $this->joinCompany($user, $invitation);

        return redirect()->route('dashboard')->with('success', 'Account created! You have joined '.$invitation->company->name);
    }

    /**
     * Common method to join company
     */
    protected function joinCompany(User $user, CompanyInvitation $invitation)
    {
        // Attach user to company if not already attached
        if (! $invitation->company->members()->where('user_id', $user->id)->exists()) {
            $invitation->company->members()->attach($user->id, ['role' => $invitation->role]);
        }

        // Update invitation status
        $invitation->update(['status' => 'accepted']);

        // Switch to this company
        session(['selected_company_id' => $invitation->company_id]);
    }

    /**
     * Remove member
     */
    public function removeMember($userId)
    {
        $companyId = session('selected_company_id');
        $company = Company::findOrFail($companyId);

        // Prevent removing self or owner
        if ($userId == Auth::id() || $userId == $company->user_id) {
            return back()->withErrors(['error' => 'Cannot remove yourself or the company owner.']);
        }

        $company->members()->detach($userId);

        return back()->with('success', 'Member removed successfully.');
    }

    /**
     * Update member role
     */
    public function updateRole(Request $request, $userId)
    {
        $request->validate(['role' => 'required|in:admin,manager,buyer,staff,approver,purchasing_manager,finance']);

        $companyId = session('selected_company_id');
        $company = Company::findOrFail($companyId);

        $company->members()->updateExistingPivot($userId, ['role' => $request->role]);

        return back()->with('success', 'Role updated successfully.');
    }
}
