<?php

namespace App\Modules\Procurement\Domain\Models;

use App\Modules\Catalogue\Domain\Models\CatalogueItem;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'purchase_requisition_id',
        'catalogue_item_id',
        'quantity',
        'price',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }

    public function catalogueItem(): BelongsTo
    {
        return $this->belongsTo(CatalogueItem::class);
    }
}
