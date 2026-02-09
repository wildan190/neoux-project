<?php

namespace Modules\Company\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'email',
        'role',
        'token',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
