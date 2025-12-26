<?php

namespace App\Modules\User\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Domain\Models\UserNotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Define all available notification keys
        $availableSettings = [
            'pr_status_updates' => 'Purchase Requisition status updates',
            'new_offers' => 'New offers received for your PRs',
            'po_received' => 'Purchase Order received (for Vendors)',
            'po_confirmed' => 'Purchase Order confirmed (for Buyers)',
            'goods_receipt' => 'Goods Receipt updates',
            'invoices' => 'Invoice updates',
            'comments' => 'New comments on your PRs',
        ];

        // Fetch current settings from DB
        $settings = $user->notificationSettings()->pluck('is_enabled', 'setting_key')->toArray();

        return view('settings', compact('availableSettings', 'settings'));
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $settings = $request->input('settings', []);

        // Define expected keys again for validation/cleanup
        $availableKeys = [
            'pr_status_updates',
            'new_offers',
            'po_received',
            'po_confirmed',
            'goods_receipt',
            'invoices',
            'comments',
        ];

        foreach ($availableKeys as $key) {
            $isEnabled = isset($settings[$key]) && $settings[$key] == '1';

            UserNotificationSetting::updateOrCreate(
                ['user_id' => $user->id, 'setting_key' => $key],
                ['is_enabled' => $isEnabled]
            );
        }

        return redirect()->route('settings.index')->with('success', 'Notification settings updated successfully.');
    }
}
