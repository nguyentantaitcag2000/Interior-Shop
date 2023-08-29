<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProductImage extends Model
{
    use HasFactory;
    protected $table = "detail_product_image";
    public $primaryKey = 'ID_DPI';
    protected $fillable = [
        'ID_Product',
        'Image'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'ID_Product');
    }
}
