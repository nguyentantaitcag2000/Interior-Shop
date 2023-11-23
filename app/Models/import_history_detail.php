<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class import_history_detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'Price_IDH',
        'Amount_IDH',
        'ID_Color',
        'ID_Material',
        'ID_D',
        'ID_Product',
        'ID_IH',
    ];
    /**
     * Lấy ra số lượng tồn của sản phẩm
    */

    static function getAmount($ID_Color, $ID_Material, $ID_D, $ID_Product )
    {
        $amountImport = import_history_detail::where('ID_Color',$ID_Color)
        ->where('ID_Material',$ID_Material)
        ->where('ID_D',$ID_D)
        ->where('ID_Product',$ID_Product)
        ->sum('Amount_IDH');
        $amountExport = ShoppingCart::where('ID_CS', '!=', 1)
        ->with(['cart_detail' => function($query) use($ID_Product) {
            $query->where('ID_Product', $ID_Product);
        }])
        ->get()
        ->sum(function ($shoppingCart) {
            return $shoppingCart->cart_detail->sum('Amount_CD');
        });

        $amount = $amountImport - $amountExport;
        $amount = $amount > 0 ? $amount : 0;
        return $amount;
    }
}
