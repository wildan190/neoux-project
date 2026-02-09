<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebitNote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'dn_number',
        'goods_return_request_id',
        'purchase_order_id',
        'original_amount',
        'adjusted_amount',
        'deduction_amount',
        'reason',
        'approved_by_vendor_at',
    ];

    protected $casts = [
        'approved_by_vendor_at' => 'datetime',
    ];

    public function goodsReturnRequest(): BelongsTo
    {
        return $this->belongsTo(GoodsReturnRequest::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function isApprovedByVendor(): bool
    {
        return !is_null($this->approved_by_vendor_at);
    }
}
