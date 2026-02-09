<?php

namespace Modules\Catalogue\Models;

use Modules\Company\Models\Company;
use Modules\Procurement\Models\PurchaseRequisitionItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'catalogue_product_id',
        'category_id', // Deprecated, moved to Product
        'sku',
        'name', // Deprecated, moved to Product
        'description', // Deprecated, moved to Product
        'tags',
        'price',
        'stock',
        'unit',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->belongsTo(CatalogueProduct::class, 'catalogue_product_id');
    }

    public function category()
    {
        return $this->belongsTo(CatalogueCategory::class, 'category_id');
    }

    public function attributes()
    {
        return $this->hasMany(CatalogueItemAttribute::class);
    }

    public function images()
    {
        return $this->hasMany(CatalogueItemImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(CatalogueItemImage::class)->where('is_primary', true);
    }

    public function purchaseRequisitionItems()
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    /**
     * Get all tags as array
     */
    public function getTagsArrayAttribute()
    {
        return $this->tags ? explode(',', $this->tags) : [];
    }

    /**
     * Generate SKU for company
     */
    public static function generateSKU($companyId, $categoryId = null)
    {
        $company = Company::find($companyId);
        $companyPrefix = strtoupper(substr($company->name, 0, 3));

        $categoryPrefix = 'GEN';
        if ($categoryId) {
            $category = CatalogueCategory::find($categoryId);
            $categoryPrefix = strtoupper(substr($category->name, 0, 3));
        }

        $lastItem = self::where('company_id', $companyId)->latest('id')->first();
        $number = $lastItem ? (intval(substr($lastItem->sku, -5)) + 1) : 1;

        return sprintf('%s-%s-%05d', $companyPrefix, $categoryPrefix, $number);
    }
}
