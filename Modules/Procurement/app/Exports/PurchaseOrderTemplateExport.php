<?php

namespace Modules\Procurement\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

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
                'PO-2026-SAMPLE01',
                'Sample Vendor Name',
                'issued',
                'Sample Item Name',
                '10',
                '25000',
                '250000',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'PO Number',
            'Vendor Name',
            'Status (issued/confirmed/completed)',
            'Item Name',
            'Quantity',
            'Unit Price',
            'Total Item Price',
        ];
    }

    public function title(): string
    {
        return 'PO Import Template';
    }
}
