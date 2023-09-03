<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_Order';
    public $table = 'order';
    public $timestamps = false;
    protected $fillable = [
        'ID_SM',
        'ID_MOP',
        'Customer_Name',
        'Note_O',
        'Phone_O',
        'Address_O',
    ];

    public function orderDetail()
    {
        return $this->hasMany(order_detail::class,'ID_Order');
    }
    public function bill()
    {
        return $this->hasOne(bill::class,'ID_Order');
    }
    public function methodofpayment()
    {
        return $this->belongsTo(method_of_payment::class,'ID_MOP');

    }

}
