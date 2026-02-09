<?php

namespace Modules\Procurement\Jobs;

use App\Modules\Procurement\Presentation\Http\Imports\PurchaseOrderHistoryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
            Excel::import(new PurchaseOrderHistoryImport($this->userId, $this->companyId, $this->importRole), $this->filePath, 'local');

            // Clean up temporary file
            Storage::disk('local')->delete($this->filePath);

            Log::info("PO Import completed for user ID: {$this->userId}, Role: {$this->importRole}");
        } catch (\Exception $e) {
            Log::error("PO Import failed: " . $e->getMessage());
            throw $e;
        }
    }
}
