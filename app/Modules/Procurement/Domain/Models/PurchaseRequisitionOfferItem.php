<?php

namespace App\Modules\Procurement\Domain\Models;

use App\Modules\Procurement\Domain\Models\PurchaseRequisitionItem;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionOfferItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'offer_id',
        'purchase_requisition_item_id',
        'quantity_offered',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionOffer::class);
    }

    public function purchaseRequisitionItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionItem::class);
    }

    // Accessor for formatted prices
    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 2, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 2, ',', '.');
    }
}
