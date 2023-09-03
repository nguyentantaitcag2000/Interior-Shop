<?php 
namespace App\Repositories\ShoppingCart;
use App\Models\order;

interface ShoppingCartRepositoryInterface{
    public function getWith($id_status,$id_user);
    public function store($id_user, $amount, $id_product);
    public function get_carts_not_checkout();
}
?>