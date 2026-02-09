<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class ReplacementDelivery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'rd_number',
        'goods_return_request_id',
        'original_goods_receipt_id',
        'expected_delivery_date',
        'actual_delivery_date',
        'status',
        'tracking_number',
        'received_by',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
    ];

    public function goodsReturnRequest(): BelongsTo
    {
        return $this->belongsTo(GoodsReturnRequest::class);
    }

    public function originalGoodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class, 'original_goods_receipt_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
