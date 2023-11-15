<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_SaleOf_Product extends Model
{
    use HasFactory;
    protected $table = "Detail_SaleOf_Product";
    public $timestamps = false;

    protected $fillable=[
        'ID_SO',
        'ID_Product',
    ];

    public function saleOff()
    {
        return $this->belongsTo(Sale_Off::class,"ID_SO");
    }
}
