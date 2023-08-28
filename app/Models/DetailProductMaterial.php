<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProductMaterial extends Model
{
    use HasFactory;
    protected $table = "detail_product_material";
    protected $fillable = [
        'ID_Product',
        'ID_Material'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class,"ID_Product");
    }
    public function material()
    {
        return $this->belongsTo(Material::class,"ID_Material");
    }
}
