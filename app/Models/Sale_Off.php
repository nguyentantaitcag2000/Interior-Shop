<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale_Off extends Model
{
    use HasFactory;
    protected $table = "Sale_Off";
    public $timestamps = false;

    public $primaryKey = 'ID_SO';
    protected $fillable=[
        'Name_SO',
        'Discount_Percent_SO',
        'Start_Date_SO',
        'End_Date_SO',
    ];
    // Hàm giải quyết khoảng thời gian sale
    public static function SolveStatusSale(&$sales)
    {
        $currentTimestamp = time();
        

        foreach ($sales as $key => $sale) {
            $startTimestamp = strtotime($sale->saleOff->Start_Date_SO);
            $endTimestamp = strtotime($sale->saleOff->End_Date_SO);

            if ($currentTimestamp >= $startTimestamp && $currentTimestamp <= $endTimestamp) {
                // Ngày hiện tại nằm trong khoảng thời gian sale
                $sale['Apply'] = 1;
            } elseif ($currentTimestamp > $endTimestamp) {
                // Ngày khuyến mãi đã từng được áp dụng
                $sale['Apply'] = 2;
            } else {
                // Ngày khuyến mãi sẽ được áp dụng trong tương lai
                $sale['Apply'] = 3;
            }
        }
    }
    // Hàm kiểm tra khoảng thời gian sale
    public static function applySaleCondition($query, $currentTimestamp)
    {
        $query->whereHas('saleOff', function ($subQuery) use ($currentTimestamp) {
            $subQuery->where('Start_Date_SO', '<=', date('Y-m-d', $currentTimestamp))
                ->where('End_Date_SO', '>=', date('Y-m-d', $currentTimestamp));
        });
    }
}
