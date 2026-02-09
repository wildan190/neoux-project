<?php

namespace Modules\Catalogue\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'company_id',
        'catalogue_item_id',
        'warehouse_id',
        'user_id',
        'type',
        'quantity',
        'previous_stock',
        'current_stock',
        'reference_type',
        'reference_id',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(\Modules\User\Models\User::class);
    }

    public function item()
    {
        return $this->belongsTo(CatalogueItem::class, 'catalogue_item_id');
    }

    public function company()
    {
        return $this->belongsTo(\Modules\Company\Models\Company::class);
    }
}
