<?php

namespace App\Modules\Catalogue\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'type',
        'status',
        'file_name',
        'total_rows',
        'processed_rows',
        'error_message',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Modules\User\Domain\Models\User::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Modules\Company\Domain\Models\Company::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_rows == 0) {
            return 0;
        }

        return round(($this->processed_rows / $this->total_rows) * 100, 2);
    }
}
