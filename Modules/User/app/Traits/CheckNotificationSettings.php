<?php

namespace Modules\User\Traits;

use Modules\User\Models\UserNotificationSetting;

trait CheckNotificationSettings
{
    /**
     * Check if a specific notification type is enabled for the user.
     *
     * @param  \Modules\User\Models\User  $user
     */
    protected function isNotificationEnabled($user, string $settingKey): bool
    {
        $setting = UserNotificationSetting::where('user_id', $user->id)
            ->where('setting_key', $settingKey)
            ->first();

        // If no setting found, default to enabled (true)
        return $setting ? $setting->is_enabled : true;
    }
}
