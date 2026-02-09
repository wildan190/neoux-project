<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GoodsReceiptItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'quantity_received',
        'condition_notes',
        'item_status',
        'has_issue',
    ];

    protected $casts = [
        'has_issue' => 'boolean',
    ];

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    /**
     * Get the goods return request for this item
     */
    public function goodsReturnRequest(): HasOne
    {
        return $this->hasOne(GoodsReturnRequest::class);
    }

    /**
     * Check if item has any issue (damaged or rejected)
     */
    public function hasIssue(): bool
    {
        return in_array($this->item_status, ['damaged', 'rejected']);
    }

    /**
     * Get item status color for badge
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->item_status) {
            'good' => 'green',
            'damaged' => 'yellow',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get item status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->item_status) {
            'good' => 'Baik',
            'damaged' => 'Rusak',
            'rejected' => 'Ditolak',
            default => '-',
        };
    }
}
