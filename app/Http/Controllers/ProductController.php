<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Product;
use Illuminate\Http\Request;
=======
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
>>>>>>> 4697d1a96321a96cfed8cef76639544953e0903e

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
<<<<<<< HEAD
        $products = Product::latest('ID_Product')->get();
=======
      
        $products = Product::select([
            'product.Size', 'product.ID_Product', 'product.ID_Category', 
            'product.Name_Product', 'product.Description', 'product.Price', 'product.Avatar',
            'ID_S'
     
        ])
        ->with('detailProductImage')
        ->with('category')
        ->with(['detailProductMaterial','detailProductMaterial.material'])
        ->with(['detailProductColor','detailProductColor.color'])
        ->orderByDesc('product.ID_Product')
        ->get();
    
>>>>>>> 4697d1a96321a96cfed8cef76639544953e0903e
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
<<<<<<< HEAD
        return Product::create([
            'ID_Category' => request('ID_Category'),
            'Name_Product' => request('Name_Product'),
            'Description' => request('Description'),
            'Price' => request('Price'),
            'Avatar' => request('Avatar'),
            'Size' => request('Size'),
            'Amount_Product' => request('Amount_Product'),
            'ID_S' => request('ID_S'),
        ]);
    }
=======
        try{

        
            return DB::transaction(function(){
                // Đường dẫn cơ bản đến thư mục sản phẩm
                $timestamp = Carbon::now()->timestamp;
                $baseDirectoryImage = "images/products/product_" . $timestamp;
                $filenameAVT = $timestamp . '_main' . '.jpg';
                $imagePathAVT = $baseDirectoryImage . '/' . $filenameAVT;
                // Chuyển base64 thành dữ liệu hình ảnh thực sự
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('Avatar')));
                // Lưu hình ảnh vào thư mục mong muốn
                Storage::disk('public')->put($imagePathAVT, $imageData);


                $ID_Materials = request('ID_Material');
                $ID_Colors = request('ID_Color');
                $DetailImages = request('DetailImage');
                $product = Product::create([
                    'ID_Category' => request('ID_Category'),
                    'Name_Product' => request('Name_Product'),
                    'Description' => request('Description'),
                    'Price' => request('Price'),
                    'Avatar' => '/storage/' . $imagePathAVT,
                    'Size' => request('Size'),
                    'ID_S' => request('ID_S'),
                ]);
                
                $productID = $product->ID_Product;
                // MATERIAL
                // MATERIAL
                // MATERIAL
                $materialsToInsert = [];
                for ($i = 0; $i < count($ID_Materials); $i++) {
                    $materialsToInsert[] = [
                        'ID_Product' => $productID,
                        'ID_Material' => $ID_Materials[$i]
                    ];
                }
                DetailProductMaterial::insert($materialsToInsert);
                // COLOR
                // COLOR
                // COLOR
                $colorsToInsert = [];
                for ($i = 0; $i < count($ID_Colors); $i++) {
                    $colorsToInsert[] = [
                        'ID_Product' => $productID,
                        'ID_Color' => $ID_Colors[$i]
                    ];
                }
                DetailProductColor::insert($colorsToInsert);
                
                // DETAIL IMAGE
                // DETAIL IMAGE
                // DETAIL IMAGE
                $imagesToInsert = [];

                

                for ($i = 0; $i < count($DetailImages); $i++) {
                    // Tạo tên tệp dựa trên timestamp hiện tại để đảm bảo rằng mỗi tên tệp là duy nhất
                    $filename = $timestamp . '_' . $i . '.jpg';

                    $imagePath = $baseDirectoryImage . '/' . $filename;

                    // Chuyển base64 thành dữ liệu hình ảnh thực sự
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $DetailImages[$i]));

                    // Lưu hình ảnh vào thư mục mong muốn
                    Storage::disk('public')->put($imagePath, $imageData);

                    // Thêm đường dẫn hình ảnh vào mảng để chèn vào cơ sở dữ liệu
                    $imagesToInsert[] = [
                        'ID_Product' => $productID,
                        'Image' => '/storage/' . $imagePath
                    ];
                }

                DetailProductImage::insert($imagesToInsert);
                // Sau khi tạo sản phẩm, truy vấn lại sản phẩm với các quan hệ bạn muốn
                $productWithRelations = Product::with('detailProductImage')
                                                ->with('category')
                                                ->with(['detailProductMaterial','detailProductMaterial.material'])
                                                ->with(['detailProductColor','detailProductColor.color'])
                ->where('ID_Product', $product->ID_Product)
                ->first();
                return response()->json([
                    'status' => 200,
                    'message' => 'Success',
                    'object' => $productWithRelations
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
>>>>>>> 4697d1a96321a96cfed8cef76639544953e0903e

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
<<<<<<< HEAD
        //
=======
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
    
                // // Xóa hình ảnh chính
                // Storage::disk('public')->delete($avatarPath);
    
                // // Xóa danh sách hình ảnh chi tiết
                // Storage::disk('public')->delete($detailImagesPaths);
    
                // Xóa sản phẩm khỏi cơ sở dữ liệu
                $product->delete();
                
                // Xác định và xóa thư mục cha chứa các hình ảnh
                // Do cả hình ảnh avt và detail đều chung 1 thư mục nên ta xóa bằng cách này:
                $avatarParts = explode('/', $product->Avatar);
                $desiredParts = array_slice($avatarParts, 0, -1); // Lấy tất cả các phần từ trừ phần tử cuối
                $desiredPath = implode('/', $desiredParts); // Ghép lại thành chuỗi
                $desiredPath = str_replace('/storage/', '', $desiredPath);
                // Để xóa thư mục, bạn chỉ cần xóa thư mục cha (không cần toàn bộ đường dẫn)
                Storage::disk('public')->deleteDirectory($desiredPath);


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
    
>>>>>>> 4697d1a96321a96cfed8cef76639544953e0903e
    }
}
