<?php

namespace App\Modules\Procurement\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PurchaseRequisitionOfferDocument extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'offer_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionOffer::class);
    }

    // Accessor for download URL
    public function getDownloadUrlAttribute(): string
    {
        return route('procurement.offers.download-document', $this);
    }

    // Accessor for formatted file size
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    // Delete file from storage when model is deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        });
    }
}
