<?php

namespace App\Modules\Procurement\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisitionOffer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'purchase_requisition_id',
        'company_id',
        'user_id',
        'status',
        'total_price',
        'notes',
        'rank_score',
        'is_recommended',
        'delivery_time',
        'warranty',
        'payment_scheme',
        'bidding_status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'rank_score' => 'decimal:2',
        'is_recommended' => 'boolean',
    ];

    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

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
        return $this->hasMany(PurchaseRequisitionOfferItem::class, 'offer_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionOfferDocument::class, 'offer_id');
    }

    public function purchaseOrder(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Modules\Procurement\Domain\Models\PurchaseOrder::class, 'offer_id');
    }

    // Accessor for formatted price
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 2, ',', '.');
    }

    // Accessor for rank position
    public function getRankPositionAttribute(): int
    {
        return PurchaseRequisitionOffer::where('purchase_requisition_id', $this->purchase_requisition_id)
            ->where('rank_score', '>', $this->rank_score)
            ->count() + 1;
    }

    // Scope for ranking offers
    public function scopeRanked($query)
    {
        return $query->orderBy('rank_score', 'desc')->orderBy('created_at', 'asc');
    }

    // Scope for pending offers
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
