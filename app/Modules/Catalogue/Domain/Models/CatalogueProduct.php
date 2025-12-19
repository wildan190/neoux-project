<?php

namespace App\Modules\Catalogue\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class CatalogueProduct extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'slug',
        'description',
        'brand',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(CatalogueCategory::class, 'category_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function items()
    {
        return $this->hasMany(CatalogueItem::class, 'catalogue_product_id');
    }

    /**
     * Get the price range of the product based on its items (SKUs)
     */
    /**
     * Get the price range of the product based on its items (SKUs)
     */
    public function getPriceRangeAttribute()
    {
        $min = $this->items->min('price');
        $max = $this->items->max('price');

        if ($min == $max) {
            return $min;
        }

        return [$min, $max];
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'brand' => $this->brand,
        ];
    }
}
