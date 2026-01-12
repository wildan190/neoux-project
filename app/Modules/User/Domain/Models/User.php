<?php

namespace App\Modules\User\Domain\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Modules\Auth\Application\Notifications\QueuedVerifyEmail);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Modules\Auth\Application\Notifications\QueuedResetPassword($token));
    }

    /**
     * Get the companies the user belongs to (as a member).
     */
    public function companies()
    {
        return $this->belongsToMany(\App\Modules\Company\Domain\Models\Company::class, 'company_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the companies the user owns.
     */
    public function ownedCompanies()
    {
        return $this->hasMany(\App\Modules\Company\Domain\Models\Company::class, 'user_id');
    }

    /**
     * Get all companies the user has access to (owned + member).
     */
    public function allCompanies()
    {
        $owned = $this->ownedCompanies;
        $member = $this->companies;

        return $owned->merge($member)->unique('id');
    }

    /**
     * Get the user's detail information.
     */
    public function userDetail()
    {
        return $this->hasOne(\App\Modules\User\Domain\Models\UserDetail::class);
    }

    /**
     * Get the user's notification settings.
     */
    public function notificationSettings()
    {
        return $this->hasMany(\App\Modules\User\Domain\Models\UserNotificationSetting::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    /**
     * The channels the user receives notification broadcasts on.
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.' . $this->id;
    }
}

