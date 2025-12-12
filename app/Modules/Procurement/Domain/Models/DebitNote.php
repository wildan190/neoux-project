<?php

namespace App\Modules\Procurement\Domain\Models;

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
        'original_amount' => 'decimal:2',
        'adjusted_amount' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'approved_by_vendor_at' => 'datetime',
    ];

    /**
     * Boot function to generate DN number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->dn_number)) {
                $model->dn_number = 'DN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
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
     * Get the purchase order
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get formatted original amount
     */
    public function getFormattedOriginalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->original_amount, 0, ',', '.');
    }

    /**
     * Get formatted adjusted amount
     */
    public function getFormattedAdjustedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->adjusted_amount, 0, ',', '.');
    }

    /**
     * Get formatted deduction amount
     */
    public function getFormattedDeductionAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->deduction_amount, 0, ',', '.');
    }

    /**
     * Get deduction percentage
     */
    public function getDeductionPercentageAttribute(): float
    {
        if ($this->original_amount == 0)
            return 0;
        return round(($this->deduction_amount / $this->original_amount) * 100, 2);
    }

    /**
     * Check if approved by vendor
     */
    public function isApprovedByVendor(): bool
    {
        return $this->approved_by_vendor_at !== null;
    }
}
