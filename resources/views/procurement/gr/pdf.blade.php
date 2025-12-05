<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>DO - {{ $goodsReceipt->gr_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            padding: 25px;
            color: white;
            margin-bottom: 20px;
        }

        .header-flex {
            display: table;
            width: 100%;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-right {
            text-align: right;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-box {
            display: table-cell;
            width: 48%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .info-box:first-child {
            margin-right: 4%;
        }

        .info-box h3 {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .info-box p {
            margin: 3px 0;
        }

        .details-grid {
            display: table;
            width: 100%;
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .detail-item {
            display: table-cell;
            width: 25%;
            padding: 5px;
        }

        .detail-label {
            color: #666;
            font-size: 9px;
            font-weight: bold;
        }

        .detail-value {
            color: #000;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background: #f5f5f5;
            padding: 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background: #ECFDF5;
            font-weight: bold;
            border-top: 2px solid #059669;
        }

        .total-amount {
            font-size: 16px;
            color: #059669;
        }

        .note-box {
            background: #FEF3C7;
            border-left: 3px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            width: 31%;
            text-align: center;
            padding: 10px;
            margin-right: 3%;
        }

        .signature-line {
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 60px;
        }

        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 8px;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-flex">
            <div class="header-left">
                <div style="font-size: 18px; font-weight: bold;">NeoUX</div>
                <div style="font-size: 10px; opacity: 0.9;">Platform by HUNTR</div>
            </div>
            <div class="header-right">
                <h1>DELIVERY ORDER</h1>
                <div style="font-size: 16px; font-family: monospace;">{{ $goodsReceipt->gr_number }}</div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>From (Vendor)</h3>
            <p style="font-weight: bold; font-size: 13px;">{{ $goodsReceipt->purchaseOrder->vendorCompany->name }}</p>
            <p>{{ $goodsReceipt->purchaseOrder->vendorCompany->email }}</p>
            @if($goodsReceipt->purchaseOrder->vendorCompany->phone)
                <p>{{ $goodsReceipt->purchaseOrder->vendorCompany->phone }}</p>
            @endif
        </div>
        <div class="info-box">
            <h3>To (Buyer)</h3>
            <p style="font-weight: bold; font-size: 13px;">
                {{ $goodsReceipt->purchaseOrder->purchaseRequisition->company->name }}</p>
            <p>{{ $goodsReceipt->purchaseOrder->purchaseRequisition->company->email }}</p>
            @if($goodsReceipt->purchaseOrder->purchaseRequisition->company->phone)
                <p>{{ $goodsReceipt->purchaseOrder->purchaseRequisition->company->phone }}</p>
            @endif
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-item">
            <div class="detail-label">DO Date</div>
            <div class="detail-value">{{ $goodsReceipt->received_at->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">PO Number</div>
            <div class="detail-value">{{ $goodsReceipt->purchaseOrder->po_number }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Delivery Note #</div>
            <div class="detail-value">{{ $goodsReceipt->delivery_note_number ?: '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Received By</div>
            <div class="detail-value">{{ $goodsReceipt->receivedBy->name }}</div>
        </div>
    </div>

    <h3 style="font-size: 11px; margin: 20px 0 10px 0;">DELIVERED ITEMS</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Item Description</th>
                <th class="text-center" style="width: 12%;">Qty Ordered</th>
                <th class="text-center" style="width: 12%;">Qty Delivered</th>
                <th style="width: 20%;">Condition</th>
            </tr>
        </thead>
        <tbody>
            @foreach($goodsReceipt->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name }}</strong><br>
                        <span style="font-size: 9px; color: #666;">SKU:
                            {{ $item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->sku }}</span>
                    </td>
                    <td class="text-center">{{ $item->purchaseOrderItem->quantity_ordered }}</td>
                    <td class="text-center"><strong style="color: #059669;">{{ $item->quantity_received }}</strong></td>
                    <td>{{ $item->condition_notes ?: 'Good' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right" style="padding: 15px;">TOTAL ITEMS DELIVERED</td>
                <td class="text-center total-amount" style="padding: 15px;">
                    {{ $goodsReceipt->items->sum('quantity_received') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if($goodsReceipt->notes)
        <div class="note-box">
            <h3 style="font-size: 11px; margin-bottom: 10px;">SPECIAL NOTES</h3>
            <p>{{ $goodsReceipt->notes }}</p>
        </div>
    @endif

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">
                <div style="font-weight: bold;">Delivered By</div>
                <div style="font-size: 9px; color: #666;">Vendor Representative</div>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <div style="font-weight: bold;">Received By</div>
                <div style="font-size: 9px; color: #666;">{{ $goodsReceipt->receivedBy->name }}</div>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <div style="font-weight: bold;">Acknowledged By</div>
                <div style="font-size: 9px; color: #666;">Warehouse Manager</div>
            </div>
        </div>
    </div>

    <div class="footer">
        This is a computer-generated delivery order. No signature is required for printing.<br>
        Generated on {{ now()->format('d F Y, H:i') }}
    </div>
</body>

</html>