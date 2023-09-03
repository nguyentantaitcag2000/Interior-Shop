<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_detail extends Model
{
    use HasFactory;

    public $table = 'order_detail';
    public $timestamps = false;
    protected $fillable = [
        'ID_Order',
        'ID_SC',
    ];

    public function shoppingCart()
    {
        return $this->belongsTo(ShoppingCart::class,'ID_SC');
    }

}
