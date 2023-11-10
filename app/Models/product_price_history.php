<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_price_history extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'ID_Product',
    ];
}
