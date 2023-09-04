<?php 
namespace App\Repositories\ShoppingCart;
use App\Models\order;

interface ShoppingCartRepositoryInterface{
    public function getWith($id_status,$id_user);
    public function store($id_user, $amount, $id_product,$id_color,$id_material);
    public function get_carts_not_checkout();
}
?>