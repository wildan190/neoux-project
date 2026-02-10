<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;
use Modules\Company\Models\Company;
use Modules\User\Models\User;

class PurchaseRequisition extends Model
{
    use HasFactory, HasUuids, Searchable;

    protected $fillable = [
        'pr_number',
        'company_id',
        'user_id',
        'title',
        'description',
        'status',
        'approval_status',
        'approval_notes',
        'approver_id',
        'head_approver_id',
        'assigned_to',
        'submitted_at',
        'winning_offer_id',
        'tender_status',
        'po_generated_at',
        'type',
        'delivery_point',
    ];

    protected $casts = [
        'po_generated_at' => 'datetime',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'pr_number' => $this->pr_number,
            'status' => $this->status,
        ];
    }

    public function approver()
    {
        return $this->belongsTo(\Modules\User\Models\User::class, 'approver_id');
    }

    public function headApprover()
    {
        return $this->belongsTo(\Modules\User\Models\User::class, 'head_approver_id');
    }

    public function assignee()
    {
        return $this->belongsTo(\Modules\User\Models\User::class, 'assigned_to');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionDocument::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionComment::class)->whereNull('parent_id')->latest();
    }

    public function offers(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionOffer::class);
    }

    public function winningOffer(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionOffer::class, 'winning_offer_id');
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(\Modules\Procurement\Models\PurchaseOrder::class);
    }
}
