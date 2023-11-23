<?php

namespace App\Http\Controllers;

use App\Models\import_history;
use App\Models\import_history_detail;
use App\Models\Product;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository) {
        $this->authRepository = $authRepository;
    }
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
        $this->authRepository->CheckLogin();
        return DB::transaction(function() use ($request){
            $selectedColorImport = $request->input('selectedColorImport');
            $selectedMaterialImport = $request->input('selectedMaterialImport');
            $selectedDimensionsImport = $request->input('selectedDimensionsImport');
            $amountImport = $request->input('amountImport');
            $priceImport = $request->input('priceImport');
            $ID_Product = $request->input('ID_Product');

            $importHistory = import_history::create([
                'TotalMoney_IH' => $priceImport * $amountImport
            ]);
            $id_ih = $importHistory->ID_IH;
            import_history_detail::create([
                'Price_IDH' => $priceImport,
                'Amount_IDH' => $amountImport,
                'ID_Color' => $selectedColorImport,
                'ID_Material' =>$selectedMaterialImport ,
                'ID_D' => $selectedDimensionsImport,
                'ID_Product' => $ID_Product,
                'ID_IH' => $id_ih,
            ]);

            //Khi import xong thì cập nhật lại số lượng tồn kho của sản phẩm
            Product::where('ID_Product',$ID_Product)->increment('Amount_Product', $amountImport);
            return json_encode([
                'status' => 200,
                'message' => 'Nhập hàng thành công',
            ]);
        });
        
    }

    /**
     * Display the specified resource.
     */
    public function show(import_history $import_history)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(import_history $import_history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, import_history $import_history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(import_history $import_history)
    {
        //
    }
}
