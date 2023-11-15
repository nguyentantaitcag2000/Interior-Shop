<?php

namespace App\Http\Controllers;

use App\Models\Detail_SaleOf_Product;
use App\Models\Sale_Off;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleOffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale_Off::orderBy('ID_SO','desc')->get();
        return json_encode([
            'status' => 200,
            "message" => "Insert successfully",
            "sales" => $sales
        ]);
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
        $saleOff = new Sale_Off();
        $saleOff->Name_SO = $request->input('name');
        $saleOff->Discount_Percent_SO = $request->input('percent');
        $saleOff->Start_Date_SO = date('Y-m-d H:i:s', $request->input('startTimestamp') / 1000);
        $saleOff->End_Date_SO = date('Y-m-d H:i:s', $request->input('endTimestamp') / 1000);
        $saleOff->save();
        
        return json_encode([
            'status' => 200,
            "message" => "Insert successfully",
            "sale" => $saleOff
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sales = Detail_SaleOf_Product::with('saleOff')->where('ID_Product',$id)->orderBy('ID_SO','desc')->get();
        //Tính toán xem sales nào đang được áp dụng

        $currentTimestamp = time();

        Sale_Off::SolveStatusSale($sales);
       
        return json_encode([
            'status' => 200,
            'message' => 'success',
            'sales' => $sales
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale_Off $sale_Off)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{

            
            return DB::transaction(function() use ($request,$id){
                
                $saleOff = Sale_Off::find($id);
                $saleOff->Name_SO = $request->input('name');
                $saleOff->Discount_Percent_SO = $request->input('percent');
                $saleOff->Start_Date_SO = date('Y-m-d H:i:s', $request->input('startTimestamp') / 1000);
                $saleOff->End_Date_SO = date('Y-m-d H:i:s', $request->input('endTimestamp') / 1000);
                $saleOff->save();

                
                return response()->json([
                    'status' => 200,
                    'message' => 'Success',
                    'object' => $saleOff
                ], 200); // Mã trạng thái HTTP 200 cho response
            },5); 
            
        }
        catch (\Exception $e) {
            // return response()->json([
            //     'status' => $e->getCode() ?: 500,
            //     'message' => 'Failed',
            //     'object' =>  $e->getMessage()
            // ], 500); // khi return ra 500 sẽ bị lỗi văng ra cả axios
            return response()->json([
                'status' => $e->getCode() ?: 500,
                'message' => 'Failed',
                'object' =>  $e->getMessage()
            ]); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            return DB::transaction(function() use ($id) {
                // Lấy sản phẩm dựa trên ID
                $sale = Sale_Off::find($id);
    
                if (!$sale) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Product not found'
                    ], 404);
                }
    
                
                $sale->delete();
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Delete sale successfully'
                ], 200);
    
            }, 5);
        } catch (\Exception $e) {
            return [
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
