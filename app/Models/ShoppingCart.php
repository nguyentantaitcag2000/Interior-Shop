<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;
    protected $table = 'shoppingcart';
    protected $primaryKey = 'ID_SC';
    public $timestamps = false;
    protected $fillable = [
        'ID_User',
        'ID_CS',
    ];
    public function cart_detail()
    {
        return $this->hasMany(CartDetail::class,'ID_SC');
    }

}
