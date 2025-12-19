<?php

namespace App\Modules\Procurement\Domain\Models;

use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Boot function to generate RD number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->rd_number)) {
                $model->rd_number = 'RD-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
            }
        });
    }

    /**
     * Get the goods return request
     */
    public function goodsReturnRequest(): BelongsTo
    {
        return $this->belongsTo(GoodsReturnRequest::class);
    }

    /**
     * Get the original goods receipt
     */
    public function originalGoodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class, 'original_goods_receipt_id');
    }

    /**
     * Get the user who received the replacement
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'shipped' => 'blue',
            'received' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pengiriman',
            'shipped' => 'Dalam Perjalanan',
            'received' => 'Sudah Diterima',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    /**
     * Check if replacement is received
     */
    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    /**
     * Check if replacement is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
