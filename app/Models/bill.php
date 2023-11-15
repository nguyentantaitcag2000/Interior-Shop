<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bill extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_Bill';
    public $table = 'bill';
    public $timestamps = false;
    protected $fillable = [
        'TotalMoney',
        'TotalMoneyAfterSaleOff',
        'VAT_rate',
        'VAT_amount',
        'TotalMoneyCheckout',
        'ID_BS',
        'ID_Order',
    ];
    public function order(){
        return $this->belongsTo(order::class,'ID_Order');
    }
    public function bill_status(){
        return $this->belongsTo(bill_status::class,'ID_BS');
    }
}
