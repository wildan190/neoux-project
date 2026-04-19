<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Catalogue\Models\CatalogueItem;

class ContractItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'procurement_contract_items';

    protected $fillable = [
        'contract_id',
        'catalogue_item_id',
        'fixed_price',
        'currency',
    ];

    protected $casts = [
        'fixed_price' => 'decimal:2',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function catalogueItem(): BelongsTo
    {
        return $this->belongsTo(CatalogueItem::class, 'catalogue_item_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->fixed_price, 2, ',', '.');
    }
}
