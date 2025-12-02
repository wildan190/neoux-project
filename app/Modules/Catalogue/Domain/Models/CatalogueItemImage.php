<?php

namespace App\Modules\Catalogue\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalogue_item_id',
        'image_path',
        'is_primary',
        'order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function catalogueItem()
    {
        return $this->belongsTo(CatalogueItem::class);
    }
}
