<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Company\Models\Company;

class CompanyActivity extends Model
{
    protected $fillable = [
        'company_id',
        'admin_id',
        'action',
        'description',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
