<?php

namespace App\Modules\Catalogue\Application\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CatalogueTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [
            [
                'Example Product Name',   // name
                'Example Description',    // description
                'Product Brand',          // brand
                'Electronics',            // category (name)
                'SKU-001',                // sku
                '150000',                 // price
                '50',                     // stock
                'Pcs',                    // unit (e.g., Pcs, Box)
                'tag1,tag2',              // tags (comma separated)
                'Color:Red,Size:XL',      // attributes (key:value pairs)
            ],
            [
                'Another Product',
                'Another Description',
                'Generic Brand',
                'Office Supplies',
                'SKU-002',
                '5000',
                '100',
                'Box',
                'promo',
                '',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Description',
            'Brand',
            'Category',
            'SKU',
            'Price',
            'Stock',
            'Unit',
            'Tags',
            'Attributes',
        ];
    }

    public function title(): string
    {
        return 'Product Import Template';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
