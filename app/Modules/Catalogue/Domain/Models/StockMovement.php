<?php

namespace App\Modules\Catalogue\Domain\Models;

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
        return $this->belongsTo(\App\Modules\User\Domain\Models\User::class);
    }

    public function item()
    {
        return $this->belongsTo(CatalogueItem::class, 'catalogue_item_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Modules\Company\Domain\Models\Company::class);
    }
}
