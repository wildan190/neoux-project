<?php

namespace App\Modules\User\Application\Traits;

use App\Modules\User\Domain\Models\UserNotificationSetting;

trait CheckNotificationSettings
{
    /**
     * Check if a specific notification type is enabled for the user.
     *
     * @param  \App\Modules\User\Domain\Models\User  $user
     * @param  string  $settingKey
     * @return bool
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
