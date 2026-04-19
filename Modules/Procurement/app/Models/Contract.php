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
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'active' => 'emerald',
            'expired' => 'red',
            'terminated' => 'orange',
            default => 'gray',
        };
    }
}
