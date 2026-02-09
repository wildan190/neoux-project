<?php

namespace Modules\Catalogue\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Catalogue\Imports\CatalogueImport;
use Modules\Catalogue\Models\ImportJob;

class ImportCatalogueItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $companyId;
    protected $importJobId;

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

        if (!$importJob) {
            return;
        }

        $importJob->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            Excel::import(new CatalogueImport($this->companyId, $this->importJobId), $this->filePath, 'local');

            $importJob->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } catch (\Exception $e) {
            $importJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
            throw $e;
        }
    }
}
