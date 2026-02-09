<?php

namespace Modules\Catalogue\Models;

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

    /**
     * Get the correct URL for the image.
     * 
     * @return string
     */
    public function getUrlAttribute()
    {
        if (str_contains($this->image_path, 'assets/img/products/')) {
            return asset($this->image_path);
        }

        return asset('storage/' . $this->image_path);
    }
}
