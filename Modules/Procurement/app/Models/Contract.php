<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Company\Models\Company;
use Modules\User\Models\User;

class Contract extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'procurement_contracts';

    protected $fillable = [
        'company_id',
        'vendor_company_id',
        'contract_number',
        'title',
        'start_date',
        'end_date',
        'status',
        'source_po_id',
        'created_by_user_id',
        'notes',
        'vendor_signed_at',
        'vendor_signed_by_user_id',
        'buyer_approved_at',
        'buyer_approved_by_user_id',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'vendor_signed_at' => 'datetime',
        'buyer_approved_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'vendor_company_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContractItem::class, 'contract_id');
    }

    public function sourcePo(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'source_po_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function vendorSignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_signed_by_user_id');
    }

    public function buyerApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_approved_by_user_id');
    }

    public function relatedRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class, 'contract_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'proposed' => 'blue',
            'signed' => 'indigo',
            'active' => 'emerald',
            'rejected' => 'red',
            'expired' => 'red',
            'terminated' => 'orange',
            default => 'gray',
        };
    }
}
