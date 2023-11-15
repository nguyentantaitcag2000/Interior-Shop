<?php

namespace App\Http\Controllers;

use App\Models\Detail_SaleOf_Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailSaleOfProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Detail_SaleOf_Product $detail_SaleOf_Product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Detail_SaleOf_Product $detail_SaleOf_Product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Ở đây sẽ tiến hành thêm hoặc update
        return DB::transaction(function() use($request){
            
            $id_product = $request->input('ID_Product');
            $id_sales = $request->input('id_sales');

            $this->insertDetail($id_sales, $id_product);

            return json_encode([
                'status' => 200,
                'message' => 'Success',
                "object" => Detail_SaleOf_Product::where('ID_Product',$id_product)->get()
            ]);
        });
    }
    private function insertDetail($array_id,$productID)
    {
        $dataToInsert = [];
        for ($i = 0; $i < count($array_id); $i++) {
            $dataToInsert[] = [
                'ID_Product' => $productID,
                'ID_SO' => $array_id[$i]
            ];
        }
        Detail_SaleOf_Product::where('ID_Product',$productID)->delete();
        Detail_SaleOf_Product::insert($dataToInsert);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Detail_SaleOf_Product $detail_SaleOf_Product)
    {
        //
    }
}
