<?php

namespace Modules\Catalogue\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CatalogueTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Name',
            'Category',
            'Brand',
            'Description',
            'SKU',
            'Price',
            'Stock',
            'Unit',
        ];
    }

    public function array(): array
    {
        return [];
    }
}
