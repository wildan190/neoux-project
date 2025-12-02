<?php

namespace App\Modules\Catalogue\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'category_id',
        'sku',
        'name',
        'description',
        'tags',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
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
