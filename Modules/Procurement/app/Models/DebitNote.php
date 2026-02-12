<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DebitNote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'dn_number',
        'status',
        'goods_return_request_id',
        'purchase_order_id',
        'original_amount',
        'deduction_percentage',
        'adjusted_amount',
        'deduction_amount',
        'reason',
        'approved_by_vendor_at',
    ];

    protected static function booted()
    {
        static::creating(function ($dn) {
            if (empty($dn->dn_number)) {
                $dn->dn_number = 'DN-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

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

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getFormattedOriginalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->original_amount, 0, ',', '.');
    }

    public function getFormattedAdjustedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->adjusted_amount, 0, ',', '.');
    }

    public function getFormattedDeductionAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->deduction_amount, 0, ',', '.');
    }

    public function isApprovedByVendor(): bool
    {
        return $this->status === 'approved';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
