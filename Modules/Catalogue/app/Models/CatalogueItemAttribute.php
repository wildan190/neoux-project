<?php

namespace Modules\Catalogue\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueItemAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalogue_item_id',
        'attribute_key',
        'attribute_value',
    ];

    public function catalogueItem()
    {
        return $this->belongsTo(CatalogueItem::class);
    }
}
