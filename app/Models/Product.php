<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use  HasFactory;
    protected $table = 'product';
    public $timestamps = false;
    protected $primaryKey = 'ID_Product';
    
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

    public function detailProductImage()
    {
        return $this->hasMany(DetailProductImage::class,'ID_Product');
    }
    public function detailProductMaterial()
    {
        return $this->hasMany(DetailProductMaterial::class,'ID_Product');
    }
    public function detailProductColor()
    {
        return $this->hasMany(DetailProductColor::class,"ID_Product");
    }
    public function dimensions()
    {
        return $this->hasMany(dimensions::class,"ID_Product");
    }
    public function category()
    {
        return $this->belongsTo(Category::class,"ID_Category");
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,"ID_S");
    }

    
}
