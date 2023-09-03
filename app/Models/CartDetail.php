<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    use HasFactory;
    protected $table = 'cart_detail';
    // public $incrementing = false;
    
    protected $fillable = [
        'ID_SC',
        'ID_Product',
        'Amount_CD'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class,'ID_Product');
    }
    public function shoppingcart()
    {
        return $this->belongsTo(ShoppingCard::class,'ID_SC');
    }
}
