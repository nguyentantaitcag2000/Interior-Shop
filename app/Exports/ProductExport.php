<?php 
namespace App\Exports;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Product::where('Amount_Product', '>', 0)->get();
    }
    public function headings(): array
    {
        return [
            "ID_Product",
            "ID_Category",
            "Name_Product",
            "Description",
            "Price",
            "Avatar",
            "Amount_Product",
            "ID_S"
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],  // In đậm hàng 1 (đây là hàng header của bạn)
        ];
    }
}
?>