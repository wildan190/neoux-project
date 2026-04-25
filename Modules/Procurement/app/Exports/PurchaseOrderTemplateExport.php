<?php

namespace Modules\Procurement\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PurchaseOrderTemplateExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * Return a collection of data.
     * For a template, we return an empty collection or sample row.
     */
    public function collection(): Collection
    {
        return collect([
            [
                '1',                        // ID
                'PR-2026-0001',             // PR Reference Number
                'Original',                 // Purchase Type
                'PO-2026-0001',             // Order No
                'Sample Buyer Company',     // Purchase Company
                '123456789',               // Purchase Company No
                'buyer@example.com',        // Purchase Company Email
                'Finance',                  // Dept
                'April',                    // Month
                'Sample Vendor',            // Vendor
                'Sample Item Description',  // Description
                'Ballpoint Pen',            // Inventory name
                'Blue Ink, 0.5mm',          // Specifications
                'Office Supplies',          // Business Category
                'Stationery',              // Category
                'PCS',                      // Primary UOM
                '10',                       // Qty
                'IDR',                      // Currency
                '5000',                     // Orgi Curr Unit Price
                '5000',                     // Unit price in original currency
                '50000',                    // Amount in original currency
                '5500',                     // Tax amount in original currency
                '55000',                    // Original Currency Total Amount
                '55000',                    // Price in Indonesia Rupiah
                '2026-04-25',              // Expected receiving date
                'Admin User',              // Created By
                'Manager User',            // Approved by
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'PR Reference Number',
            'Purchase Type',
            'Order No',
            'Purchase Company',
            'Purchase Company No',
            'Purchase Company Email',
            'Dept',
            'Month',
            'Vendor',
            'Description',
            'Inventory name',
            'Specifications',
            'Business Category',
            'Category',
            'Primary UOM',
            'Qty',
            'Currency',
            'Orgi Curr Unit Price',
            'Unit price in original currency',
            'Amount in original currency',
            'Tax amount in original currency',
            'Original Currency Total Amount',
            'Price in Indonesia Rupiah',
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
