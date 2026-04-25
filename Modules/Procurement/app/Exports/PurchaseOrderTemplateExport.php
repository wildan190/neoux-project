<?php

namespace Modules\Procurement\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PurchaseOrderTemplateExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection(): Collection
    {
        return collect([
            [
                1,                              // SN
                '',                             // Select
                'PR/SER24010002',               // PR Refference Number
                'Original purchase',            // Purchase Type
                'PO/EXS24010006',              // Order No
                'Service-EX SP',               // Purchase Category
                '',                             // Purchase Contract No
                '',                             // Purchase Contract_head
                '02/01/24',                    // Date
                1,                              // Month
                'PT. SAMPLE VENDOR',           // Vendor
                'Finance Department',          // Department
                'John Doe',                    // Clerk
                'IDR',                         // Currency
                1,                              // Exchange rate
                'INV-001',                     // Inventory Code
                '',                            // Category
                'Sample Item Name',            // Inventory name
                '',                            // Specifications
                'Pc',                          // Primary UOM
                1,                              // Qty
                '1.000.000',                   // Orgi Curr Unit Price
                '1.000.000',                   // Unit price in original currency
                '1.000.000',                   // Amount in original currency
                '110.000',                     // Tax amount in original currency
                '1.110.000',                   // Original Currency Total Amount
                '02/01/24',                    // Expected receiving date
                'John Doe',                    // Created By
                'Manager Name',                // Approved by
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'SN',
            'Select',
            'PR Refference Number',
            'Purchase Type',
            'Order No',
            'Purchase Category',
            'Purchase Contract No',
            'Purchase Contract_head',
            'Date',
            'Month',
            'Vendor',
            'Department',
            'Clerk',
            'Currency',
            'Exchange rate',
            'Inventory Code',
            'Category',
            'Inventory name',
            'Specifications',
            'Primary UOM',
            'Qty',
            'Orgi Curr Unit Price',
            'Unit price in original currency',
            'Amount in original currency',
            'Tax amount in original currency',
            'Original Currency Total Amount',
            'Expected receiving date',
            'Created By',
            'Approved by',
        ];
    }

    public function title(): string
    {
        return 'PO Import Template';
    }
}
