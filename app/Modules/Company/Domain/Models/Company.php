<?php

namespace App\Modules\Company\Domain\Models;

use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(CompanyLocation::class);
    }
}
