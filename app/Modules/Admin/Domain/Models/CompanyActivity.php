<?php

namespace App\Modules\Admin\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use Illuminate\Database\Eloquent\Model;

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
