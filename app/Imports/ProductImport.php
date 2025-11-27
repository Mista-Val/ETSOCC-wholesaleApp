<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Product([
            'name'      => $row['name'],
            'sku'       => $row['sku'],
            'status'    => $row['status'],
            'min_price' => $row['min_price'],
            'max_price' => $row['max_price'],
            'category'  => $row['category'],
            'remarks'   => $row['remarks'],
        ]);
    }
}
