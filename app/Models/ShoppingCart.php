<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;
    protected $table = 'shoppingcart';
    protected $primarykey = 'ID_SC';
    public $timestamps = false;
    protected $fillable = [
        'ID_User'
    ];
    public function cartdetail()
    {
        return $this->hasMany(CartDetail::class,'ID_SC');
    }

}
