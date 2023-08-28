<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProductColor extends Model
{
    use HasFactory;
    protected $table = "detail_product_color";
    protected $fillable = [
        'ID_Product',
        'ID_Color'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class,"ID_Product");
    }
    public function color()
    {
        return $this->belongsTo(Color::class,"ID_Color");
    }
}
