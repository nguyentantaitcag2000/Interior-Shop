<?php

namespace App\Http\Controllers;

use App\Models\bill;
use App\Models\Bill_Status_History;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository) {
        $this->authRepository = $authRepository;
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(bill $bill)
    {
        $data = $bill->load(['order.orderDetail.shoppingCart.cart_detail.product',
        'order.orderDetail.shoppingCart.cart_detail.color',
        'order.orderDetail.shoppingCart.cart_detail.material',
        'order.orderDetail.shoppingCart.cart_detail.dimensions',
        'order.methodofpayment']);
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(bill $bill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, bill $bill)
    {
        $this->authRepository->CheckLogin();
        $id_bs = $request->input('ID_BS');
        $bill->ID_BS = $id_bs;
        $bill->save();

        
        if(session()->has('id_user') == false)
        {
            return json_encode([
                'status' => 400,
                'message' => 'Session has expired',
            ]);
        }
        // Lưu lại lịch sử thay đổi
        $bsh = new Bill_Status_History();
        $bsh->ID_User = session('id_user');
        $bsh->ID_BS = $id_bs;
        $bsh->ID_BILL = $bill->ID_Bill;
        $bsh->save();
        $myBill = bill::where('ID_Bill', $bill->ID_Bill)->with(['bill_status','billStatusHistory','billStatusHistory.user','billStatusHistory.bill_status'])->first();
        return json_encode([
            'status' => 200,
            'message' => 'Success',
            'object' => $myBill
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(bill $bill)
    {
        //
    }
    public function processing()
    {
        $data  = bill::where('ID_BS', 1)->get();
        return $data;
    }
    public function thongke()
    {
        $totalSalesData = [];
        $status = 1;
        $arrayStatus = [
            'Đang xử lý',
            'Đã thanh toán',
            'Đang vận chuyển',
            'Đã hủy đơn',
        ];
        $total = DB::select("
            SELECT COALESCE(SUM(TotalMoneyCheckout), 0) AS total
            FROM bill
            WHERE ID_BS = $status;
        ")[0]->total;

        $totalSalesData[$arrayStatus[$status-1]] = $total;

        $status = 2;
        $total = DB::select("
            SELECT COALESCE(SUM(TotalMoneyCheckout), 0) AS total
            FROM bill
            WHERE ID_BS = $status;
        ")[0]->total;

        $totalSalesData[$arrayStatus[$status-1]] = $total;


        $status = 3;
        $total = DB::select("
            SELECT COALESCE(SUM(TotalMoneyCheckout), 0) AS total
            FROM bill
            WHERE ID_BS = $status;
        ")[0]->total;

        $totalSalesData[$arrayStatus[$status-1]] = $total;

        $status = 4;
        $total = DB::select("
            SELECT COALESCE(SUM(TotalMoneyCheckout), 0) AS total
            FROM bill
            WHERE ID_BS = $status;
        ")[0]->total;

        $totalSalesData[$arrayStatus[$status-1]] = $total;
        return $totalSalesData;
    }
}
