<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice - {{ $invoice->purchaseOrder->invoice_number }}</title>
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
            background: linear-gradient(135deg, #EC6A2D 0%, #F5C343 100%);
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

        .logo {
            height: 40px;
            margin-bottom: 5px;
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
            background: #FFF3E0;
            font-weight: bold;
            border-top: 2px solid #EC6A2D;
        }

        .total-amount {
            font-size: 16px;
            color: #EC6A2D;
        }

        .terms {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 3px solid #EC6A2D;
        }

        .terms h3 {
            font-size: 11px;
            margin-bottom: 10px;
        }

        .terms ul {
            list-style: none;
            font-size: 9px;
            color: #666;
        }

        .terms li {
            margin: 5px 0;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            width: 48%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 15px;
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

@php
    $company = $invoice->purchaseOrder->vendorCompany;
    $logoPath = $company->logo ? public_path('storage/' . $company->logo) : null;
    $logoData = "";
    if ($logoPath && file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $mimeType = mime_content_type($logoPath);
    }
@endphp

<body>
    <div class="header">
        <div class="header-flex">
            <div class="header-left">
                @if($logoData)
                    <img src="data:{{ $mimeType }};base64,{{ $logoData }}" class="logo">
                @else
                    <div style="font-size: 18px; font-weight: bold;">{{ $company->name }}</div>
                @endif
                <div style="font-size: 10px; opacity: 0.9;">Powered by HUNTR</div>
            </div>
            <div class="header-right">
                <h1>INVOICE</h1>
                <div style="font-size: 16px; font-family: monospace;">{{ $invoice->purchaseOrder->invoice_number }}
                </div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>Vendor</h3>
            <p style="font-weight: bold; font-size: 13px;">{{ $invoice->purchaseOrder->vendorCompany->name }}</p>
            <p>{{ $invoice->purchaseOrder->vendorCompany->email }}</p>
            @if($invoice->purchaseOrder->vendorCompany->phone)
                <p>{{ $invoice->purchaseOrder->vendorCompany->phone }}</p>
            @endif
        </div>
        <div class="info-box">
            <h3>Buyer</h3>
            <p style="font-weight: bold; font-size: 13px;">
                {{ $invoice->purchaseOrder->purchaseRequisition->company->name }}
            </p>
            <p>{{ $invoice->purchaseOrder->purchaseRequisition->company->email }}</p>
            @if($invoice->purchaseOrder->purchaseRequisition->company->phone)
                <p>{{ $invoice->purchaseOrder->purchaseRequisition->company->phone }}</p>
            @endif
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-item">
            <div class="detail-label">PO Date</div>
            <div class="detail-value">{{ $invoice->purchaseOrder->created_at->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">PR Number</div>
            <div class="detail-value">{{ $invoice->purchaseOrder->purchaseRequisition->pr_number }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Status</div>
            <div class="detail-value">{{ strtoupper($invoice->purchaseOrder->status) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Created By</div>
            <div class="detail-value">{{ $invoice->purchaseOrder->createdBy->name }}</div>
        </div>
    </div>

    <h3 style="font-size: 11px; margin: 20px 0 10px 0;">ORDER ITEMS</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Item Description</th>
                <th class="text-center" style="width: 10%;">Qty</th>
                <th class="text-right" style="width: 15%;">Unit Price</th>
                <th class="text-right" style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->purchaseOrder->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->purchaseRequisitionItem->catalogueItem->name }}</strong><br>
                        <span style="font-size: 9px; color: #666;">SKU:
                            {{ $item->purchaseRequisitionItem->catalogueItem->sku }}</span>
                    </td>
                    <td class="text-center"><strong>{{ $item->quantity_ordered }}</strong></td>
                    <td class="text-right">{{ $item->formatted_unit_price }}</td>
                    <td class="text-right"><strong>{{ $item->formatted_subtotal }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right" style="padding: 15px;">TOTAL AMOUNT</td>
                <td class="text-right total-amount" style="padding: 15px;">
                    {{ $invoice->purchaseOrder->formatted_total_amount }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="terms">
        <h3>TERMS & CONDITIONS</h3>
        <ul>
            <li>• Payment terms: Net 30 days from invoice date</li>
            <li>• Delivery must be made to the address specified by the buyer</li>
            <li>• All items must match the specifications in this purchase order</li>
            <li>• Any discrepancies must be reported within 48 hours of delivery</li>
        </ul>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div style="margin-bottom: 5px;">
                <img src="{{ App\Support\QrCodeHelper::generateBase64Svg($invoice->purchaseOrder->invoice_number . '|BUYER|' . $invoice->created_at, 60) }}" style="width: 60px;">
            </div>
            <div class="signature-line">
                <div style="font-weight: bold;">Authorized Buyer</div>
                <div style="font-size: 9px; color: #666;">
                    {{ $invoice->purchaseOrder->purchaseRequisition->company->name }}
                </div>
            </div>
        </div>
        <div class="signature-box">
            <div style="margin-bottom: 5px;">
                <img src="{{ App\Support\QrCodeHelper::generateBase64Svg($invoice->purchaseOrder->invoice_number . '|VENDOR|' . $invoice->created_at, 60) }}" style="width: 60px;">
            </div>
            <div class="signature-line">
                <div style="font-weight: bold;">Vendor Acknowledgment</div>
                <div style="font-size: 9px; color: #666;">{{ $invoice->purchaseOrder->vendorCompany->name }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        This is a computer-generated document. No signature is required.<br>
        Generated on {{ now()->format('d F Y, H:i') }}
    </div>
</body>

</html>