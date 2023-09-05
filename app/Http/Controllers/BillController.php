<?php

namespace App\Http\Controllers;

use App\Models\bill;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Http\Request;

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
        $bill->ID_BS = $request->input('ID_BS');
        $bill->save();
        return json_encode([
            'status' => 200,
            'message' => 'Success',
            'object' => $bill->load('bill_status')
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
}
