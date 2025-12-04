<?php

namespace App\Modules\Procurement\Domain\Models;

use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionComment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'purchase_requisition_id',
        'user_id',
        'parent_id',
        'comment',
    ];

    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(PurchaseRequisitionComment::class, 'parent_id')->with('user.userDetail')->oldest();
    }
}
