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
            "ID_Bill",
            "CreateDate",
            "TotalMoney",
            "VAT_rate",
            "VAT_amount",
            "TotalMoneyCheckout",
            "ID_BS",
            "ID_Order"
            // ... các tiêu đề khác tương ứng với các cột bạn muốn xuất
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