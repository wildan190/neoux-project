<?php

namespace Modules\Procurement\Jobs;

use Modules\Procurement\Imports\PurchaseOrderHistoryImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessPurchaseOrderImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    protected $userId;

    protected $companyId;

    protected $importRole;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, $userId, $companyId = null, $importRole = 'buyer')
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
        $this->companyId = $companyId;
        $this->importRole = $importRole;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (!Storage::disk('public')->exists($this->filePath)) {
                $absolutePath = Storage::disk('public')->path($this->filePath);
                Log::error("PO Import File Missing: Relative Path [{$this->filePath}], Absolute Path [{$absolutePath}]");
                throw new \Exception("File not found on public disk: {$this->filePath}");
            }

            Excel::import(new PurchaseOrderHistoryImport($this->userId, $this->companyId, $this->importRole), $this->filePath, 'public');

            // Clean up temporary file
            Storage::disk('public')->delete($this->filePath);

            Log::info("PO Import completed for user ID: {$this->userId}, Role: {$this->importRole}");
        } catch (\Exception $e) {
            Log::error('PO Import failed: '.$e->getMessage());
            throw $e;
        }
    }
}
