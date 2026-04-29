<?php

namespace Modules\Procurement\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Services\MidtransIrisService;

class ProcessDisbursementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

    protected $purchaseOrder;
    protected $referenceNo;

    /**
     * Create a new job instance.
     */
    public function __construct(PurchaseOrder $purchaseOrder, $referenceNo = null)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->referenceNo = $referenceNo ?? 'JOB-DISBURSE-' . str_replace('/', '-', $purchaseOrder->po_number) . '-' . time();
    }

    /**
     * Execute the job.
     */
    public function handle(MidtransIrisService $irisService)
    {
        $po = $this->purchaseOrder;
        $vendor = $po->vendorCompany;

        if (!$vendor || empty($vendor->bank_account_number) || empty($vendor->bank_name)) {
            Log::error("Disbursement Job failed for PO {$po->po_number}: Vendor bank details missing.");
            return;
        }

        // Avoid double disbursement if already released
        if ($po->escrow_status === 'released') {
            Log::info("Disbursement Job skipped for PO {$po->po_number}: Already released.");
            return;
        }

        $amount = $po->has_deductions ? $po->adjusted_total_amount : $po->total_amount;

        try {
            $result = $irisService->createPayout(
                $this->referenceNo,
                $amount,
                $vendor->bank_name,
                $vendor->bank_account_number,
                $vendor->bank_account_holder ?? $vendor->name,
                $vendor->email ?? ''
            );

            if ($result) {
                $po->update([
                    'escrow_status' => 'released',
                    'escrow_released_at' => now(),
                    'status' => 'completed',
                ]);
                Log::info("Disbursement Job success for PO {$po->po_number}. Reference: {$this->referenceNo}");
            } else {
                throw new \Exception("IRIS API returned failure.");
            }
        } catch (\Exception $e) {
            Log::error("Disbursement Job error for PO {$po->po_number}: " . $e->getMessage());
            // This will trigger a retry because we are in ShouldQueue
            throw $e;
        }
    }
}
