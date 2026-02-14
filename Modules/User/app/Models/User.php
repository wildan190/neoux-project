<?php

namespace Modules\User\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable, TwoFactorAuthenticatable, HasRoles;

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
        $this->notify(new \Modules\Auth\Notifications\QueuedVerifyEmail);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \Modules\Auth\Notifications\QueuedResetPassword($token));
    }

    /**
     * Get the companies the user belongs to (as a member).
     */
    public function companies()
    {
        return $this->belongsToMany(\Modules\Company\Models\Company::class, 'company_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the companies the user owns.
     */
    public function ownedCompanies()
    {
        return $this->hasMany(\Modules\Company\Models\Company::class, 'user_id');
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
        return $this->hasOne(\Modules\User\Models\UserDetail::class);
    }

    /**
     * Check if user is the owner of the given company.
     */
    public function isOwner($companyId): bool
    {
        return $this->ownedCompanies()->where('id', $companyId)->exists();
    }

    /**
     * Get user's role in a specific company.
     */
    public function getRoleInCompany($companyId): ?string
    {
        // Set the team ID for Spatie
        setPermissionsTeamId($companyId);

        // Owner has 'owner' virtual role
        if ($this->isOwner($companyId)) {
            return 'owner';
        }

        return $this->getRoleNames()->first();
    }

    /**
     * Check if user has one of the given roles in a specific company.
     * 'owner' always bypasses and returns true.
     */
    public function hasCompanyRole($companyId, $roles): bool
    {
        if ($this->isOwner($companyId)) {
            return true;
        }

        setPermissionsTeamId($companyId);

        $roles = (array) $roles;
        return $this->hasAnyRole($roles);
    }

    /**
     * Check if user has a specific permission in a specific company.
     * 'owner' always bypasses and returns true.
     */
    public function hasCompanyPermission($companyId, $permission): bool
    {
        if ($this->isOwner($companyId)) {
            return true;
        }

        setPermissionsTeamId($companyId);

        return $this->hasPermissionTo($permission);
    }

    /**
     * Get the user's notification settings.
     */
    public function notificationSettings()
    {
        return $this->hasMany(\Modules\User\Models\UserNotificationSetting::class);
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
