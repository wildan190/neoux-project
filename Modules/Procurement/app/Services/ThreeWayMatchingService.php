<?php

namespace Modules\Procurement\Services;

use Modules\Procurement\Models\Invoice;

class ThreeWayMatchingService
{
    public function match(Invoice $invoice): array
    {
        $purchaseOrder = $invoice->purchaseOrder;
        $purchaseOrder->load('items', 'goodsReceipts.items');

        $matchResults = [
            'status' => 'matched', // Default to matched, change if mismatch found
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
            $priceMatch = abs($priceVariance) < 0.01; // Tolerance

            // 2. Quantity Match (Invoice Qty vs GR Qty)
            // Calculate total received quantity for this item across all GRs
            $totalReceived = 0;
            foreach ($purchaseOrder->goodsReceipts as $gr) {
                $grItem = $gr->items->where('purchase_order_item_id', $poItem->id)->first();
                if ($grItem) {
                    $totalReceived += $grItem->quantity_received;
                }
            }

            // Note: In a real scenario, we might match against specific GRs.
            // Here we check if total invoiced so far + current invoice qty <= total received.
            // For simplicity, we just check if Invoice Qty <= Total Received (assuming one invoice per PO for now or cumulative check)

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
}
