<?php

namespace App\Modules\Procurement\Domain\Models;

use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * Boot function to generate GRR number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->grr_number)) {
                $model->grr_number = 'GRR-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
            }
        });
    }

    /**
     * Get the goods receipt item that has the issue
     */
    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }

    /**
     * Get the user who created this GRR
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the debit note for price adjustment resolution
     */
    public function debitNote(): HasOne
    {
        return $this->hasOne(DebitNote::class);
    }

    /**
     * Get the replacement delivery for replacement resolution
     */
    public function replacementDelivery(): HasOne
    {
        return $this->hasOne(ReplacementDelivery::class);
    }

    /**
     * Check if GRR is pending
     */
    public function isPending(): bool
    {
        return $this->resolution_status === 'pending';
    }

    /**
     * Check if GRR is resolved
     */
    public function isResolved(): bool
    {
        return $this->resolution_status === 'resolved';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->resolution_status) {
            'pending' => 'yellow',
            'approved_by_vendor' => 'blue',
            'rejected_by_vendor' => 'red',
            'resolved' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get issue type label
     */
    public function getIssueTypeLabelAttribute(): string
    {
        return match ($this->issue_type) {
            'damaged' => 'Barang Rusak',
            'rejected' => 'Ditolak (Tidak Sesuai Spec)',
            'wrong_item' => 'Salah Barang',
            default => $this->issue_type,
        };
    }

    /**
     * Get resolution type label
     */
    public function getResolutionTypeLabelAttribute(): string
    {
        return match ($this->resolution_type) {
            'price_adjustment' => 'Penyesuaian Harga (Debit Note)',
            'replacement' => 'Penggantian Unit Baru',
            'return_refund' => 'Pengembalian & Refund',
            default => '-',
        };
    }
}
