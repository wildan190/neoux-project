<?php

namespace App\Modules\Procurement\Domain\Models;

use App\Modules\User\Domain\Models\User;
use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use App\Modules\Company\Domain\Models\Company;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOffer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseRequisition extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'description',
        'status',
        'winning_offer_id',
        'tender_status',
        'po_generated_at',
    ];

    protected $casts = [
        'po_generated_at' => 'datetime',
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

    public function offers(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionOffer::class);
    }

    public function winningOffer(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionOffer::class, 'winning_offer_id');
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(\App\Modules\Procurement\Domain\Models\PurchaseOrder::class);
    }
}
