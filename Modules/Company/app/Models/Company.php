<?php

namespace Modules\Company\Models;

use Modules\Catalogue\Models\CatalogueItem;
use Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'business_category',
        'category',
        'status',
        'logo',
        'npwp',
        'email',
        'website',
        'phone',
        'tag',
        'country',
        'registered_date',
        'address',
        'description',
        'approved_by',
        'approved_at',
        'declined_by',
        'declined_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function locations()
    {
        return $this->hasMany(CompanyLocation::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(\Modules\Admin\Models\Admin::class, 'approved_by');
    }

    public function declinedBy()
    {
        return $this->belongsTo(\Modules\Admin\Models\Admin::class, 'declined_by');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(\Modules\Procurement\Models\PurchaseRequisitionOffer::class);
    }

    public function purchaseRequisitions(): HasMany
    {
        return $this->hasMany(\Modules\Procurement\Models\PurchaseRequisition::class);
    }

    public function catalogueItems(): HasMany
    {
        return $this->hasMany(CatalogueItem::class);
    }

    public function activities()
    {
        return $this->hasMany(\Modules\Admin\Models\CompanyActivity::class)->latest();
    }

    /**
     * Get the full URL for the company logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return asset('storage/' . $this->logo);
    }

    /**
     * Get the team members including owner
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'company_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get pending invitations
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(CompanyInvitation::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\CompanyFactory::new();
    }
}
