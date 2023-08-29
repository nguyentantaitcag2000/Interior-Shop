<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\DetailProductColor;
use App\Models\DetailProductImage;
use App\Models\DetailProductMaterial;
use App\Models\Material;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
        $products = $this->getRelates(Product::select([
            'product.Size', 'product.ID_Product', 'product.ID_Category', 
            'product.Name_Product', 'product.Description', 'product.Price', 'product.Avatar',
            'ID_S'
        ]))
        ->orderByDesc('product.ID_Product')
        ->get();
    
        return $products;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

        
            return DB::transaction(function(){
                $Image = new Image();
                

                $ID_Materials = request('ID_Material');
                $ID_Colors = request('ID_Color');
                $DetailImages = request('DetailImage');
                $product = Product::create([
                    'ID_Category' => request('ID_Category'),
                    'Name_Product' => request('Name_Product'),
                    'Description' => request('Description'),
                    'Price' => request('Price'),
                    'Avatar' =>  $Image->imagePathAVT_Full,
                    'Size' => request('Size'),
                    'ID_S' => request('ID_S'),
                ]);
                
                $productID = $product->ID_Product;
                // MATERIAL
                // MATERIAL
                // MATERIAL
                $this->insertDetailMaterial($ID_Materials,$productID);
                // COLOR
                // COLOR
                // COLOR
                $this->insertDetailColor($ID_Colors,$productID);
                // DETAIL IMAGE
                // DETAIL IMAGE
                // DETAIL IMAGE

                $Image->UploadAVT(request('Avatar'));
                $Image->UploadAndDetailImage_And_Database($DetailImages,$productID);
             
                return response()->json([
                    'status' => 200,
                    'message' => 'Success',
                    'object' => $this->show( $product->ID_Product)
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
        return $this->getRelates(Product::query()) 
        ->where('ID_Product', $id)
        ->first();
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
                $myProduct = Product::find($id);
                $oldFolderImage = $myProduct->Avatar;

                $ID_Materials = request('ID_Material');
                $ID_Colors = request('ID_Color');
                $DetailImages = request('DetailImage');
                
                foreach (Product::find($id)->detailProductImage as $row) {
                    $row->delete();
                }
                $myProduct->ID_Category = request('ID_Category');
                $myProduct->Name_Product = request('Name_Product');
                $myProduct->Description = request('Description');
                $myProduct->Price = request('Price');
                $myProduct->Avatar = $Image->imagePathAVT_Full;
                $myProduct->Size = request('Size');
                $myProduct->ID_S = request('ID_S');

                $myProduct->save();
                
                $productID = $id;
                // MATERIAL
                // MATERIAL
                // MATERIAL
                $this->insertDetailMaterial($ID_Materials,$productID);
                // COLOR
                // COLOR
                // COLOR
                $this->insertDetailColor($ID_Colors,$productID);
                // DETAIL IMAGE
                // DETAIL IMAGE
                // DETAIL IMAGE

                $Image->UploadAVT(request('Avatar'));
                $Image->UploadAndDetailImage_And_Database($DetailImages,$productID);
             
                //Sau khi xong thì xóa đi folder image cũ
                $this->deleteFolderImage($oldFolderImage);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Success',
                    'object' => $this->show($productID)
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
                $product = Product::find($id);
    
                if (!$product) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Product not found'
                    ], 404);
                }
    
                // Lấy đường dẫn hình ảnh chính (Avatar)
                $avatarPath = str_replace('/storage/', '', $product->Avatar);
    
                // // Lấy danh sách đường dẫn hình ảnh chi tiết
                // $detailImages = DetailProductImage::where('ID_Product', $id)->pluck('Image')->toArray();
                // $detailImagesPaths = array_map(function($path) {
                //     return str_replace('/storage/', '', $path);
                // }, $detailImages);
    
                
    
                // Xóa sản phẩm khỏi cơ sở dữ liệu
                $product->delete();
                
                
                $this->deleteFolderImage($product->Avatar);

                return response()->json([
                    'status' => 200,
                    'message' => 'Product and its images deleted successfully'
                ], 200);
    
            }, 5);
        } catch (\Exception $e) {
            return [
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage()
            ];
        }
    
    }
    private function getRelates($table)
    {
        return $table->with('detailProductImage')
        ->with('category')
        ->with(['detailProductMaterial' => function($query){ $query->distinct()->groupBy(['ID_Material','ID_Product']);}
            ,'detailProductMaterial.material'])
        ->with(['detailProductColor' => function($query){ $query->distinct()->groupBy(['ID_Color', 'ID_Product']); }
            ,'detailProductColor.color']);
    }
   
    private function insertDetailMaterial($array_id_materials,$productID)
    {
        $materialsToInsert = [];
        for ($i = 0; $i < count($array_id_materials); $i++) {
            $materialsToInsert[] = [
                'ID_Product' => $productID,
                'ID_Material' => $array_id_materials[$i]
            ];
        }
        DetailProductMaterial::insert($materialsToInsert);
    }
    private function insertDetailColor($array_id_colors,$productID)
    {
        $colorsToInsert = [];
        for ($i = 0; $i < count($array_id_colors); $i++) {
            $colorsToInsert[] = [
                'ID_Product' => $productID,
                'ID_Color' => $array_id_colors[$i]
            ];
        }
        DetailProductColor::insert($colorsToInsert);
    }
    
   
    private function deleteFolderImage($pathAvatar)
    {
        // // Xóa hình ảnh chính
        // Storage::disk('public')->delete($avatarPath);

        // // Xóa danh sách hình ảnh chi tiết
        // Storage::disk('public')->delete($detailImagesPaths);
        // Xác định và xóa thư mục cha chứa các hình ảnh
        // Do cả hình ảnh avt và detail đều chung 1 thư mục nên ta xóa bằng cách này:
        $avatarParts = explode('/', $pathAvatar);
        $desiredParts = array_slice($avatarParts, 0, -1); // Lấy tất cả các phần từ trừ phần tử cuối
        $desiredPath = implode('/', $desiredParts); // Ghép lại thành chuỗi
        $desiredPath = str_replace('/storage/', '', $desiredPath);
        // Để xóa thư mục, bạn chỉ cần xóa thư mục cha (không cần toàn bộ đường dẫn)
        Storage::disk('public')->deleteDirectory($desiredPath);
    }
}
class Image {
    private $timestamp = null;
    public $imagePathAVT = null; 
    public $imagePathAVT_Full = null;
    public function __construct() {
        $this->timestamp = Carbon::now()->timestamp;
        // Đường dẫn cơ bản đến thư mục sản phẩm
        $baseDirectoryImage = "images/products/product_" . $this->timestamp;
        $filenameAVT = $this->timestamp . '_main' . '.jpg';
        $this->imagePathAVT =  $baseDirectoryImage . '/' . $filenameAVT;
        $this->imagePathAVT_Full = '/storage/' . $this->imagePathAVT;
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
    public function UploadAndDetailImage_And_Database($array_base64, $productID) {
        $baseDirectoryImage = "images/products/product_" . $this->timestamp;
        $imagesToInsert = [];
    
        for ($i = 0; $i < count($array_base64); $i++) {
            $filename = $this->timestamp . '_' . $i . '.jpg';
            $imagePath = $baseDirectoryImage . '/' . $filename;
            
            // Kiểm tra xem chuỗi có phải là base64 hay không
            if ($this->isBase64($array_base64[$i])) {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $array_base64[$i]));
                Storage::disk('public')->put($imagePath, $imageData);
            } else {
                // Nếu không phải là base64 thì giả định nó là một đường dẫn hình ảnh
                $array_base64[$i] = str_replace('/storage/','',$array_base64[$i]);
                Storage::disk('public')->copy($array_base64[$i], $imagePath);
            }
    
            $imagePathForDB = '/storage/' . $imagePath;
            $imagesToInsert[] = [
                'ID_Product' => $productID,
                'Image' => $imagePathForDB
            ];
        }
        DetailProductImage::insert($imagesToInsert);
    }
    // public function UploadAndDetailImage_And_Database($array_base64,$productID)
    // {
    //     // Đường dẫn cơ bản đến thư mục sản phẩm
    //     $baseDirectoryImage = "images/products/product_" . $this->timestamp;
    //     $imagesToInsert = [];
    //     for ($i = 0; $i < count($array_base64); $i++) {
    //         // Tạo tên tệp dựa trên timestamp hiện tại để đảm bảo rằng mỗi tên tệp là duy nhất
    //         $filename = $this->timestamp . '_' . $i . '.jpg';

    //         $imagePath = $baseDirectoryImage . '/' . $filename;

    //         // Chuyển base64 thành dữ liệu hình ảnh thực sự
    //         $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $array_base64[$i]));

    //         // Lưu hình ảnh vào thư mục mong muốn
    //         Storage::disk('public')->put($imagePath, $imageData);

    //         // Thêm đường dẫn hình ảnh vào mảng để chèn vào cơ sở dữ liệu
    //         $imagesToInsert[] = [
    //             'ID_Product' => $productID,
    //             'Image' => '/storage/' . $imagePath
    //         ];
    //     }
    //     DetailProductImage::insert($imagesToInsert);
    // }
}
