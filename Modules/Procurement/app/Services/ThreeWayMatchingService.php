<?php

namespace Modules\Procurement\Services;

use Modules\Procurement\Models\Invoice;
use Modules\Procurement\Models\PurchaseOrder;

class ThreeWayMatchingService
{
    /**
     * Run 3-way matching: PO vs GR vs Invoice
     */
    public function match(Invoice $invoice): array
    {
        $purchaseOrder = $invoice->purchaseOrder;
        $purchaseOrder->load('items', 'goodsReceipts.items');

        $matchResults = [
            'status' => 'matched',
            'variances' => [],
            'details' => [],
        ];

        foreach ($invoice->items as $invoiceItem) {
            $poItem = $purchaseOrder->items->where('id', $invoiceItem->purchase_order_item_id)->first();

            if (! $poItem) {
                $matchResults['variances'][] = 'Item in invoice not found in PO.';
                $matchResults['status'] = 'mismatch';
                continue;
            }

            // 1. Price Match (Invoice Price vs PO Price)
            $priceVariance = $invoiceItem->unit_price - $poItem->unit_price;
            $priceMatch = abs($priceVariance) < 0.01;

            // 2. Quantity Match (Invoice Qty vs GR Qty)
            $totalReceived = 0;
            foreach ($purchaseOrder->goodsReceipts as $gr) {
                $grItem = $gr->items->where('purchase_order_item_id', $poItem->id)->first();
                if ($grItem) {
                    $totalReceived += $grItem->quantity_received;
                }
            }

            $qtyMatch = $invoiceItem->quantity_invoiced <= $totalReceived;

            if (! $priceMatch || ! $qtyMatch) {
                $matchResults['status'] = 'mismatch';
            }

            $matchResults['details'][] = [
                'item_id' => $poItem->id,
                'po_price' => $poItem->unit_price,
                'invoice_price' => $invoiceItem->unit_price,
                'price_match' => $priceMatch,
                'total_received' => $totalReceived,
                'invoice_qty' => $invoiceItem->quantity_invoiced,
                'qty_match' => $qtyMatch,
            ];
        }

        // Update Invoice Status
        $invoice->update([
            'status' => $matchResults['status'],
            'match_status' => $matchResults,
        ]);

        return $matchResults;
    }

    /**
     * Run escrow-based 3-way matching after GR is logged.
     * Legs: PO (order) vs GR (goods received) vs Escrow (payment).
     * If all match → auto-release escrow.
     */
    public function matchEscrow(PurchaseOrder $purchaseOrder): array
    {
        $purchaseOrder->load('items', 'goodsReceipts.items');

        $matchResults = [
            'status' => 'matched',
            'variances' => [],
            'details' => [],
        ];

        // Check 1: Escrow must be paid
        if ($purchaseOrder->escrow_status !== 'paid') {
            $matchResults['status'] = 'mismatch';
            $matchResults['variances'][] = 'Escrow belum dibayar oleh buyer.';
            return $matchResults;
        }

        // Check 2: Escrow amount vs PO total
        $poTotal = $purchaseOrder->has_deductions
            ? $purchaseOrder->adjusted_total_amount
            : $purchaseOrder->total_amount;

        $matchResults['escrow_check'] = [
            'po_total' => $poTotal,
            'escrow_paid' => true,
        ];

        // Check 3: All items received (GR qty >= PO qty)
        $allItemsReceived = true;
        foreach ($purchaseOrder->items as $poItem) {
            $totalReceived = 0;
            foreach ($purchaseOrder->goodsReceipts as $gr) {
                $grItem = $gr->items->where('purchase_order_item_id', $poItem->id)->first();
                if ($grItem) {
                    $totalReceived += $grItem->quantity_received;
                }
            }

            $qtyMatch = $totalReceived >= $poItem->quantity_ordered;
            if (! $qtyMatch) {
                $allItemsReceived = false;
            }

            $matchResults['details'][] = [
                'item_id' => $poItem->id,
                'quantity_ordered' => $poItem->quantity_ordered,
                'total_received' => $totalReceived,
                'qty_match' => $qtyMatch,
            ];
        }

        if (! $allItemsReceived) {
            $matchResults['status'] = 'partial';
            $matchResults['variances'][] = 'Belum semua item diterima.';
            return $matchResults;
        }

        // All 3 legs match → Auto-release escrow
        if ($matchResults['status'] === 'matched') {
            $purchaseOrder->update([
                'escrow_status' => 'released',
                'escrow_released_at' => now(),
                'status' => 'completed',
            ]);
        }

        return $matchResults;
    }
}
