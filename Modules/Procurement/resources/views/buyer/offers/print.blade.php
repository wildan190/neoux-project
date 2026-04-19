<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Technical & Financial Analysis - {{ $offer->company->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #111; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #666; font-size: 10px; letter-spacing: 1px; }
        
        .section-title { background: #f9f9f9; padding: 8px 12px; font-weight: bold; text-transform: uppercase; font-size: 11px; margin-top: 25px; margin-bottom: 15px; border-left: 4px solid #111; }
        
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-grid td { padding: 10px; border: 1px solid #eee; width: 50%; vertical-align: top; }
        .info-label { font-size: 9px; color: #999; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .info-value { font-weight: bold; color: #111; font-size: 12px; }
        
        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items th { background: #f5f5f5; text-align: left; padding: 10px; border: 1px solid #eee; font-size: 10px; text-transform: uppercase; }
        table.items td { padding: 10px; border: 1px solid #eee; }
        
        .total-row { background: #f9f9f9; font-weight: bold; font-size: 14px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #aaa; padding: 20px 0; }
        
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-primary { background: #111; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tender Analysis Report</h1>
        <p>Reference: {{ $purchaseRequisition->pr_number }} • Generated on {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="section-title">General Information</div>
    <table class="info-grid">
        <tr>
            <td>
                <div class="info-label">Requisition Title</div>
                <div class="info-value">{{ $purchaseRequisition->title }}</div>
            </td>
            <td>
                <div class="info-label">Vendor Proposed</div>
                <div class="info-value">{{ $offer->company->name }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="info-label">Project Status</div>
                <div class="info-value">{{ strtoupper($offer->status) }}</div>
            </td>
            <td>
                <div class="info-label">Rank Score</div>
                <div class="info-value">{{ $offer->rank_score }}/100</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Financial Summary</div>
    <table class="info-grid">
        <tr>
            <td>
                <div class="info-label">Total Bid Amount</div>
                <div class="info-value">{{ $offer->formatted_total_price }}</div>
            </td>
            <td>
                <div class="info-label">Payment Scheme</div>
                <div class="info-value">{{ $offer->payment_scheme ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Technical Terms</div>
    <table class="info-grid">
        <tr>
            <td>
                <div class="info-label">Delivery Timeline</div>
                <div class="info-value">{{ $offer->delivery_time ?? 'N/A' }}</div>
            </td>
            <td>
                <div class="info-label">Warranty / Support</div>
                <div class="info-value">{{ $offer->warranty ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Itemized Analysis</div>
    <table class="items">
        <thead>
            <tr>
                <th>Product Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($offer->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold;">{{ $item->purchaseRequisitionItem->catalogueItem->name }}</div>
                        <div style="font-size: 9px; color: #888;">SKU: {{ $item->purchaseRequisitionItem->catalogueItem->sku }}</div>
                    </td>
                    <td class="text-center">{{ $item->quantity_offered }}</td>
                    <td class="text-right">{{ $item->formatted_unit_price }}</td>
                    <td class="text-right">{{ $item->formatted_subtotal }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="total-row">
            <tr>
                <td colspan="3" class="text-right">GRAND TOTAL</td>
                <td class="text-right">{{ $offer->formatted_total_price }}</td>
            </tr>
        </tfoot>
    </table>

    @if($offer->notes)
        <div class="section-title">Vendor Notes</div>
        <div style="padding: 10px; background: #fafafa; border: 1px solid #eee; font-style: italic;">
            "{{ $offer->notes }}"
        </div>
    @endif

    <div class="footer">
        Confidential Report • Neoux Procurement System • Page 1 of 1
    </div>
</body>
</html>
