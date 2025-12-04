<?php

namespace App\Modules\Procurement\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'description',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionDocument::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionComment::class)->whereNull('parent_id')->latest();
    }
}
