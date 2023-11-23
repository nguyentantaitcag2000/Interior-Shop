<?php

namespace App\Models;

use DateTime;
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
        $currentDateTime = new DateTime(); // Lấy ngày giờ hiện tại

        foreach ($sales as $key => $sale) {
            $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $sale->saleOff->Start_Date_SO);
            $endDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $sale->saleOff->End_Date_SO);

            if ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) {
                // Ngày hiện tại nằm trong khoảng thời gian sale
                $sales[$key]['Apply'] = 1;
            } elseif ($currentDateTime > $endDateTime) {
                // Ngày khuyến mãi đã từng được áp dụng
                $sales[$key]['Apply'] = 2;
            } else {
                // Ngày khuyến mãi sẽ được áp dụng trong tương lai
                $sales[$key]['Apply'] = 3;
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
