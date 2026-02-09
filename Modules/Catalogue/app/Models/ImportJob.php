<?php

namespace Modules\Catalogue\Models;

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
        return $this->belongsTo(\Modules\User\Models\User::class);
    }

    public function company()
    {
        return $this->belongsTo(\Modules\Company\Models\Company::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_rows == 0) {
            return 0;
        }

        return round(($this->processed_rows / $this->total_rows) * 100, 2);
    }
}
