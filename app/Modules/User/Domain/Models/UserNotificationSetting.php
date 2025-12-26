<?php

namespace App\Modules\User\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'setting_key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
