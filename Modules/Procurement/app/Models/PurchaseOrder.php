<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Company\Models\Company;
use Modules\User\Models\User;

class PurchaseOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'po_number',
        'company_id',
        'purchase_requisition_id',
        'offer_id',
        'vendor_company_id',
        'historical_vendor_name',
        'created_by_user_id',
        'total_amount',
        'status',
        'confirmed_at',
        'vendor_accepted_at',
        'vendor_rejected_at',
        'vendor_notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function buyerCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionOffer::class, 'offer_id');
    }

    public function vendorCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'vendor_company_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    /**
     * Get debit notes related to this PO
     */
    public function debitNotes(): HasMany
    {
        return $this->hasMany(DebitNote::class);
    }

    /**
     * Get total deduction amount from approved debit notes
     */
    public function getTotalDeductionAttribute(): float
    {
        return $this->debitNotes()
            ->whereNotNull('approved_by_vendor_at')
            ->sum('deduction_amount');
    }

    /**
     * Get adjusted total amount (after deductions)
     */
    public function getAdjustedTotalAmountAttribute(): float
    {
        return $this->total_amount - $this->total_deduction;
    }

    /**
     * Get formatted adjusted total amount
     */
    public function getFormattedAdjustedTotalAmountAttribute(): string
    {
        return 'Rp '.number_format($this->adjusted_total_amount, 2, ',', '.');
    }

    /**
     * Check if PO has any deductions
     */
    public function getHasDeductionsAttribute(): bool
    {
        return $this->total_deduction > 0;
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp '.number_format($this->total_amount, 2, ',', '.');
    }

    /**
     * Get formatted total deduction
     */
    public function getFormattedTotalDeductionAttribute(): string
    {
        return 'Rp '.number_format($this->total_deduction, 2, ',', '.');
    }
}
