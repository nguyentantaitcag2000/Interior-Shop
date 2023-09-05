<?php

namespace App\Http\Controllers;

use App\Models\CartDetail;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingCartController extends Controller
{
    public $authReponsitory;
    public $shoppingCardRepository;
    public function __construct(AuthRepositoryInterface $authReponsitory,ShoppingCartRepositoryInterface $shoppingCardRepository) {
        $this->authReponsitory = $authReponsitory;
        $this->shoppingCardRepository = $shoppingCardRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ShoppingCart::with(['cart_detail','cart_detail.product'])->get();
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authReponsitory->CheckLogin();
        
        $id_user = session('id_user');
        $amount = request('Amount');
        $id_product = request('ID_Product');
        $id_color = request('ID_Color');
        $id_material = request('ID_Material');
        $id_dimensions = request('ID_Dimensions');
        $result = $this->shoppingCardRepository->store($id_user, $amount, $id_product,$id_color,$id_material,$id_dimensions);
        return json_encode($result);
        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(ShoppingCart $shoppingCart)
    {
        $this->authReponsitory->CheckLogin();
        $user = User::where('Email',session('email'))->first();
        if( ! $user)
        {
            return json_encode([
                'status' => 401,
                'message' => 'Email not existed'
            ]);
        }

        $data = $this->shoppingCardRepository->getWith(1,session('id_user'));
        return json_encode([
            'status' => 200,
            'message' => 'Success',
            'object' => $data,
            'ID_User' => $user->ID_User
        ]);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShoppingCart $shoppingCart)
    {
        //
    }
    public function updates(Request $request)
    {
        $this->authReponsitory->CheckLogin();
        $datas = $request->input('carts');
        foreach ($datas as $key => $cart) {
            $cartDetail_arr = $cart['cart_detail'];
            foreach ($cartDetail_arr as $cartD) {
                if($cartD['Amount_CD'] <= 0)
                {
                    return json_encode([
                        'status' => 401,
                        'message' => 'Số lượng phải lớn hơn 0'
                    ]);     
                }
                CartDetail::where('ID_SC',$cartD['ID_SC'])->where('ID_Product',$cartD['ID_Product'])
                ->update(['Amount_CD' => $cartD['Amount_CD']]);
            }
        }
        return json_encode([
            'status' => 200,
            'message' => 'Updated'
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShoppingCart $shoppingCart)
    {
        if(session()->has('id_user') == false)
        {
            return json_encode([
                'status' => 401,
                'message' => 'Required Login'
            ]);
        }
        $ID_SC = $shoppingCart->ID_SC;
        ShoppingCart::where('ID_SC',$ID_SC)->delete();
        return json_encode([
            'status' => 200,
            'message' => 'Delete success',
        ]);
    }
    public function get_carts_not_checkout()
    {
        return $this->shoppingCardRepository->get_carts_not_checkout();
        
    }
    private function checkSession()
    {

    }
}
