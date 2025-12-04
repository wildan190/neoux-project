<?php

namespace App\Modules\Company\Domain\Models;

use App\Modules\User\Domain\Models\User;
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
        return $this->belongsTo(\App\Modules\Admin\Domain\Models\Admin::class, 'approved_by');
    }

    public function declinedBy()
    {
        return $this->belongsTo(\App\Modules\Admin\Domain\Models\Admin::class, 'declined_by');
    }

    public function activities()
    {
        return $this->hasMany(\App\Modules\Admin\Domain\Models\CompanyActivity::class)->latest();
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
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\CompanyFactory::new();
    }
}
