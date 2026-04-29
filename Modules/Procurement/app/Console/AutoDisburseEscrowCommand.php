<?php

namespace Modules\Procurement\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Jobs\ProcessDisbursementJob;

class AutoDisburseEscrowCommand extends Command
{
    protected $signature = 'procurement:auto-disburse';

    protected $description = 'Automatically disburse paid escrow funds to vendors based on payment terms and due dates via Queues.';

    public function handle()
    {
        $this->info('Starting Auto-Disbursement Process (Queue-based)...');

        /**
         * Target POs where:
         * - Buyer has paid escrow (escrow_status = paid)
         * - Goods have been delivered / all received (status = full_delivery, received, or completed*)
         */
        $purchaseOrders = PurchaseOrder::with([
                'vendorCompany',
                'invoices' => fn($q) => $q->latest(),
            ])
            ->where('escrow_status', 'paid')
            ->whereIn('status', ['full_delivery', 'received', 'completed'])
            ->get();

        if ($purchaseOrders->isEmpty()) {
            $this->info('No eligible Purchase Orders found for auto-disbursement.');
            return Command::SUCCESS;
        }

        $jobCount = 0;
        $skippedCount = 0;

        foreach ($purchaseOrders as $po) {
            $vendor  = $po->vendorCompany;
            $invoice = $po->invoices->first();

            // --- Guard: Vendor bank details ---
            if (!$vendor || empty($vendor->bank_account_number) || empty($vendor->bank_name)) {
                Log::warning("Cannot auto-disburse PO {$po->po_number}: Vendor bank details missing.");
                continue;
            }

            // --- Guard: Payment terms due date check ---
            if ($invoice && $invoice->due_date) {
                $dueDate = Carbon::parse($invoice->due_date)->startOfDay();
                if (now()->startOfDay()->lt($dueDate)) {
                    $skippedCount++;
                    continue;
                }
            }

            $referenceNo = 'AUTODISBURSE-Q-' . str_replace('/', '-', $po->po_number) . '-' . time();
            
            // Dispatch the job
            ProcessDisbursementJob::dispatch($po, $referenceNo);
            
            $this->info("  -> Dispatched disbursement job for PO {$po->po_number}");
            $jobCount++;
        }

        $this->info("─────────────────────────────────────────────");
        $this->info("Done. Dispatched Jobs: {$jobCount} | Skipped (not due): {$skippedCount}");

        return Command::SUCCESS;
    }
}
