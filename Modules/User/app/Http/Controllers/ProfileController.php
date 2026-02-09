<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('userDetail');

        return view('profile.show', compact('user'));
    }

    public function updateDetails()
    {
        $validated = request()->validate([
            'id_number' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        $user = auth()->user();
        $userDetail = $user->userDetail;

        // If user detail doesn't exist, create it with registered_date from user's created_at
        if (! $userDetail) {
            $validated['registered_date'] = $user->created_at;
            $user->userDetail()->create($validated);
        } else {
            $userDetail->update($validated);
        }

        return redirect()->route('profile.show')->with('status', 'profile-details-updated');
    }

    public function updatePhoto()
    {
        request()->validate([
            'profile_photo' => ['required', 'image', 'max:2048'], // 2MB max
        ]);

        $user = auth()->user();
        $userDetail = $user->userDetail;

        // Delete old photo if exists
        if ($userDetail && $userDetail->profile_photo) {
            \Storage::disk('public')->delete($userDetail->profile_photo);
        }

        // Store new photo
        $path = request()->file('profile_photo')->store('profile-photos', 'public');

        // Create or update user detail
        if (! $userDetail) {
            $user->userDetail()->create([
                'profile_photo' => $path,
                'registered_date' => $user->created_at,
            ]);
        } else {
            $userDetail->update(['profile_photo' => $path]);
        }

        return redirect()->route('profile.show')->with('status', 'profile-photo-updated');
    }

    public function deletePhoto()
    {
        $user = auth()->user();
        $userDetail = $user->userDetail;

        if ($userDetail && $userDetail->profile_photo) {
            // Delete from storage
            \Storage::disk('public')->delete($userDetail->profile_photo);

            // Update database
            $userDetail->update(['profile_photo' => null]);
        }

        return redirect()->route('profile.show')->with('status', 'profile-photo-deleted');
    }
}
