<?php

namespace Modules\Catalogue\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class CatalogueTemplateExport implements WithHeadings, FromArray
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
