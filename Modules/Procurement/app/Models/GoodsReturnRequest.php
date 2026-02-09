<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\User\Models\User;

class GoodsReturnRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'grr_number',
        'goods_receipt_item_id',
        'issue_type',
        'quantity_affected',
        'issue_description',
        'photo_evidence',
        'resolution_type',
        'resolution_status',
        'created_by',
        'resolved_at',
    ];

    protected $casts = [
        'photo_evidence' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function debitNote(): HasOne
    {
        return $this->hasOne(DebitNote::class);
    }

    public function replacementDelivery(): HasOne
    {
        return $this->hasOne(ReplacementDelivery::class);
    }
}
