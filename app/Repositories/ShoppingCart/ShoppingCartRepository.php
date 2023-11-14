<?php 
namespace App\Repositories\ShoppingCart;

use App\Models\CartDetail;
use App\Models\order;
use App\Models\ShoppingCart;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Config\Repository;

use Illuminate\Support\Facades\DB;
use function PHPSTORM_META\map;

class ShoppingCartRepository extends Repository implements ShoppingCartRepositoryInterface{
    public function getWith($id_status, $id_user)
    {
        $data = ShoppingCart::with(['cart_detail.color','cart_detail.product.detailProductColor' => function($query) {
            $query->distinct()->with('color');
        },
        'cart_detail.material','cart_detail.dimensions'])
        ->where('ID_User',$id_user)
        ->where('ID_CS',$id_status)
                ->get();
        return $data;
    }
    public function store($id_user, $amount, $id_product,$id_color,$id_material,$id_dimensions)
    {
        return DB::transaction(function() use($id_user, $amount, $id_product,$id_color,$id_material,$id_dimensions){
            
            if($amount > 0)
            {
                $SC = ShoppingCart::create([
                    'ID_User' => $id_user,
                    'ID_CS' => 1,
                ]);
                CartDetail::create([
                    'ID_SC' => $SC->ID_SC,
                    'ID_Product' => $id_product,
                    'Amount_CD' => $amount,
                    'ID_Color' => $id_color,
                    'ID_Material' => $id_material,
                    'ID_D' => $id_dimensions
                ]);
                return [
                    'status' => 200,
                    'message' => 'Thêm vào giỏ thành công !',
                    'object' => $this->get_carts_not_checkout(),
                    'ID_SC' => $SC->ID_SC
                ];
            }
            return [
                'status' => 400,
                'message' => 'Thêm vào giỏ thất bại !',
            ];
        });
    }
    public function get_carts_not_checkout()
    {
        if(session()->has('id_user') == false)
        {
            return [];
        }
        $id_user = session('id_user');
        $data = ShoppingCart::where('ID_CS',1)->where('ID_User', $id_user)->get();
        return $data;
    }
}
?>