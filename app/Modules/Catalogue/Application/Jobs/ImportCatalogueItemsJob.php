<?php

namespace App\Modules\Catalogue\Application\Jobs;

use App\Modules\Catalogue\Application\Imports\CatalogueImport;
use App\Modules\Catalogue\Domain\Models\ImportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportCatalogueItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    protected $companyId;

    protected $importJobId;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 600; // 10 minutes

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $companyId, $importJobId)
    {
        $this->filePath = $filePath;
        $this->companyId = $companyId;
        $this->importJobId = $importJobId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $importJob = ImportJob::find($this->importJobId);

        if (! $importJob) {
            \Log::error('Import job not found: '.$this->importJobId);

            return;
        }

        try {
            // Update status to processing
            $importJob->update(['status' => 'processing']);

            // Count total rows before starting import
            // Use 'local' disk and relative path
            $rows = Excel::toArray(new \stdClass, $this->filePath, 'local');
            $totalRows = isset($rows[0]) ? count($rows[0]) - 1 : 0; // Subtract header

            if ($totalRows > 0) {
                $importJob->update(['total_rows' => $totalRows]);
            }

            // Pass relative path and 'local' disk to Excel::import
            Excel::import(new CatalogueImport($this->companyId, $importJob), $this->filePath, 'local');

            // Update status to completed
            $importJob->update([
                'status' => 'completed',
                'processed_rows' => $importJob->total_rows,
            ]);

            // Delete file after successful import using local disk
            Storage::disk('local')->delete($this->filePath);
        } catch (\Exception $e) {
            // Update status to failed
            $importJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Log error
            \Log::error('Import failed: '.$e->getMessage());
            throw $e;
        }
    }
}
