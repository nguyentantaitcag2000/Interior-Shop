<?php

namespace App\Http\Controllers;

use App\Models\bill;
use App\Models\CartDetail;
use App\Models\order;
use App\Models\order_detail;
use App\Models\Product;
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
            $id_dimensions = request('idDimensionsBuyNow');
            

            $result = $this->shoppingCartRepository->store($id_user,$amount,$id_product,$id_color,$id_material,$id_dimensions);
            if($result['status'] != 200)
            {
                return json_encode([
                    'status' => 400,
                    'message' => 'Order failed',
                    'result' => $result
                ]);
            }
            $ID_SC = $result['ID_SC'];
            $array_insert_data_order_detail[] = [
                'ID_SC' => $ID_SC,
                'ID_Order' => $order->ID_Order
            ];
            //Chuyển trạng thái các giỏ hàng thành đã thanh toán
            ShoppingCart::where('ID_SC',$ID_SC)->update(['ID_CS' => 3]);
            $CartDetailList = CartDetail::where('ID_SC',$ID_SC)->with('product')->get();

            //Khi export xong thì cập nhật lại số lượng tồn kho của sản phẩm
            Product::where('ID_Product',$id_product)->decrement('Amount_Product', $amount);

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
        $totalMoneyAfterSaleOff = 0;
        $vat = 8;
        foreach ($CartDetailList as $key => $cartDetail) {
            $totalMoney += $cartDetail->product->Price * $cartDetail->Amount_CD;
            
            //Khi export xong thì cập nhật lại số lượng tồn kho của sản phẩm
            Product::where('ID_Product',$cartDetail->product->ID_Product)->decrement('Amount_Product', $cartDetail->Amount_CD);
        }
        foreach ($CartDetailList as $key => $cardetail) {
            // Tính số tiền đã được giảm giá nếu có
            $product= $cardetail->product;
            $price = $product->Price;
            if($product->detailSaleOfProduct)
            {
                foreach ($product->detailSaleOfProduct as $key2 => $detail_sale) {
                    $currentTimestamp = time();
                    $startTimestamp = strtotime($detail_sale->saleOff->Start_Date_SO);
                    $endTimestamp = strtotime($detail_sale->saleOff->End_Date_SO);

                    // Kiểm tra xem Ngày hiện tại nằm trong khoảng thời gian sale
                    if ($currentTimestamp >= $startTimestamp && $currentTimestamp <= $endTimestamp) {
                        $price -= ($product->Price * $detail_sale->saleOff->Discount_Percent_SO / 100);
                    }
                }
            }
            $product->Price_SaleOff =  $price ; 
                
            $totalMoneyAfterSaleOff += $price * $cardetail->Amount_CD;
        }
        $vat_amount = $totalMoney * $vat/100;
        $totalMoneyCheckout =  $totalMoneyAfterSaleOff + $vat_amount;

        bill::create([
            'TotalMoney' => $totalMoney,
            'TotalMoneyAfterSaleOff' => $totalMoneyAfterSaleOff,
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
    public function showAll(Request $request)
    {
      
        $data = Order::with(['orderDetail.shoppingCart','bill','bill.billStatusHistory','bill.billStatusHistory.user','bill.billStatusHistory.bill_status','orderDetail.shoppingCart.cart_detail.product','bill.bill_status'])
        ->whereHas('orderDetail.shoppingCart')
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
