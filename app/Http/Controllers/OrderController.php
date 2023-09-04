<?php

namespace App\Http\Controllers;

use App\Models\bill;
use App\Models\CartDetail;
use App\Models\order;
use App\Models\order_detail;
use App\Models\ShoppingCart;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public $authReponsitory;
    public $shoppingCartRepository;
    public function __construct(AuthRepositoryInterface $authReponsitory, ShoppingCartRepositoryInterface $shoppingCartRepository) {
        $this->authReponsitory = $authReponsitory;
        $this->shoppingCartRepository = $shoppingCartRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        //Tạo đơn đặt hàng
        $order =order::create([
            'ID_SM' => $request->input('ID_SM'),
            'ID_MOP' => $request->input('ID_MOP'),
            'Customer_Name' => $request->input('fullName'),
            'Note_O' => $request->input('note'),
            'Phone_O' => $request->input('phone'),
            'Address_O' => $request->input('address'),
        ]);
        // Tạo chi tiết đơn đặt hàng
        $array_insert_data_order_detail = array();
        //Nếu người dùng mua dạng mua ngay thì sẽ chưa có giỏ hàng ta sẽ tạo giỏ hàng
        if($request->input('isShow'))
        {
            $id_user = session('id_user');

            $amount = $request->input('amountBuyNow');
            $id_product = $request->input('idProductBuyNow');
            $id_color = $request->input('idColorBuyNow');
            $id_material = $request->input('idMaterialBuyNow');
            $result = $this->shoppingCartRepository->store($id_user,$amount,$id_product,$id_color,$id_material);
            $ID_SC = $result['ID_SC'];
            $array_insert_data_order_detail[] = [
                'ID_SC' => $ID_SC,
                'ID_Order' => $order->ID_Order
            ];
            //Chuyển trạng thái các giỏ hàng thành đã thanh toán
            ShoppingCart::where('ID_SC',$ID_SC)->update(['ID_CS' => 3]);
            $CartDetailList = CartDetail::where('ID_SC',$ID_SC)->with('product')->get();

        }
        else
            {
            foreach ($request->input('ID_SC_List') as $key => $ID_SC) {
                $array_insert_data_order_detail[] = [
                    'ID_SC' => $ID_SC,
                    'ID_Order' => $order->ID_Order,
                ];
                //Chuyển trạng thái các giỏ hàng thành đã thanh toán
                ShoppingCart::where('ID_SC',$ID_SC)->update(['ID_CS' => 3]);
            }
            $CartDetailList = CartDetail::whereIn('ID_SC',$request->input('ID_SC_List'))->with('product')->get();
        }
        
        order_detail::insert($array_insert_data_order_detail);
        // Tạo bill
        
        $totalMoney = 0;
        $vat = 8;
        foreach ($CartDetailList as $key => $cartDetail) {
            $totalMoney += $cartDetail->product->Price * $cartDetail->Amount_CD;
        }
        $vat_amount = $totalMoney * $vat/100;
        $totalMoneyCheckout =  $totalMoney + $vat_amount;

        bill::create([
            'TotalMoney' => $totalMoney,
            'VAT_rate' => $vat,
            'VAT_amount' => $vat_amount,
            'TotalMoneyCheckout' => $totalMoneyCheckout,
            'ID_BS' => 1, // Đang xử lý
            'ID_Order' => $order->ID_Order,
        ]);
        return json_encode([
            'status' => 200,
            'message' => 'Order successfull'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,order $order)
    {
        $this->authReponsitory->CheckLogin();
        $userId = session('id_user');
        
        $data = Order::with(['orderDetail.shoppingCart' => function($query) use ($userId) {
            $query->where('ID_User', $userId);
        },'orderDetail.shoppingCart.cart_detail.product','bill.bill_status'])
        ->whereHas('orderDetail.shoppingCart', function($query) use ($userId) {
            $query->where('ID_User', $userId);
        })
        ->whereHas('bill.bill_status', function($query) use ($request) {
            if($request->input('ID_BS') != 0)
                $query->where('ID_BS', $request->input('ID_BS'));
        })
        ->orderBy('ID_Order','DESC')
        ->get();

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(order $order)
    {
        //
    }
}
