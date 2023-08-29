<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorys = Category::query()->orderByDesc('ID_Category')->get();
        return $categorys;
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
        try{

        
            return DB::transaction(function(){
                $Image = new Image();
              
                $category = Category::create([
                    'Name_Category' => request('Name_Category'),
                    'Icon' => $Image->imagePathAVT_Full
                ]);
                $categoryID = $category->ID_Category;
                $Image->UploadAVT(request('Icon'));
                
             
                return response()->json([
                    'status' => 200,
                    'message' => 'Success',
                    'object' => Category::find($categoryID)
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{

        
            return DB::transaction(function() use ($id){
                $Image = new Image();
                $myData = Category::find($id);
                $oldPathImage = str_replace('/storage/','',$myData->Icon);
                $myData->Name_Category =  request('Name_Category');
                $myData->Icon = $Image->imagePathAVT_Full;
                $myData->save();

                
                $Image->UploadAVT(request('Icon'));
                
                //Sau khi xong thì xóa đi folder image cũ
                Storage::disk('public')->delete($oldPathImage);
                return response()->json([
                    'status' => 200,
                    'message' => 'Success',
                    'object' => Category::find($id)
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
        $row = Category::find($id);
        $row->delete();
        //Delete icon
        Storage::disk('public')->delete( str_replace('/storage/','',$row->Icon));
        return json_encode([
            'status' => 200,
            'message' => 'Xóa thành công !'
        ]);
    }
    
    
}
class Image {
    private $timestamp = null;
    public $imagePathAVT = null; 
    public $imagePathAVT_Full = null;
    public function __construct() {
        $this->timestamp = Carbon::now()->timestamp;
        // Đường dẫn cơ bản đến thư mục sản phẩm
        $baseDirectoryImage = "images/categories";
        $filenameAVT = $this->timestamp . '.jpg';
        $this->imagePathAVT = $baseDirectoryImage . '/' . $filenameAVT;
        $this->imagePathAVT_Full ='/storage/' . $this->imagePathAVT;
    }
    public function UploadAVT($base64){
        
        if($this->isBase64($base64))
        {
                // Chuyển base64 thành dữ liệu hình ảnh thực sự
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
                // Lưu hình ảnh vào thư mục mong muốn
                Storage::disk('public')->put($this->imagePathAVT, $imageData);
        }
        else{
            // Nếu không phải là base64 thì giả định nó là một đường dẫn hình ảnh
            $base64 = str_replace('/storage/','',$base64);
            Storage::disk('public')->copy($base64, $this->imagePathAVT);
        }
        
    }
    private function isBase64($string) {
        return preg_match('#^data:image/\w+;base64,#i', $string);
    }
    
}