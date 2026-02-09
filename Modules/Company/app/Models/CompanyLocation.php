<?php

namespace Modules\Company\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'address',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\CompanyLocationFactory::new();
    }
}
