<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use  HasFactory;
    protected $table = 'product';
    protected $fillable = [
        'ID_Category',
        'Name_Product',
        'Description',
        'Price',
        'Avatar',
        'Size',
        'Amount_Product',
        'ID_S'
    ];
}
