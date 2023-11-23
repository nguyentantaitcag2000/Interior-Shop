<?php

namespace App\Http\Controllers;

use App\Models\Review_Rating;
use Exception;
use Illuminate\Http\Request;

class ReviewRatingController extends Controller
{
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
        $content = $request->input('content');
        $value = $request->input('value');
        $idProduct = $request->input('idProduct');
        $idUser = $request->input('idUser');
        try{
            $reviewRating = new Review_Rating();
            $reviewRating->Content_RR = $content;
            $reviewRating->ID_Product = $idProduct;
            $reviewRating->ID_User = $idUser;
            $reviewRating->ID_Rate = $value;
            $reviewRating->save();
        }
        catch(Exception $exception)
        {
            return json_encode([
                'status' => 400,
                'message' => 'Đánh giá không thành công thành công',
                'detail' => $exception->getMessage()
            ]);
        }
        

        return json_encode([
            'status' => 200,
            'message' => 'Đánh giá thành công'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $idProduct)
    {
        $result = Review_Rating::with(['Rate', 'User'])
            ->whereIn('ID_Rate', range(1, 5))
            ->where('ID_Product', $idProduct)
            ->get();
        $allReviews = [];
        // Bây giờ bạn có thể lọc dữ liệu theo đánh giá cụ thể nếu cần
        $resultByRate = [];
        foreach (range(1, 5) as $i) {
            $reviews = $result->where('ID_Rate', $i)->values()->all();
            $resultByRate['_' . $i] = $reviews;

            // Ghép các đánh giá vào mảng $allReviews
            $allReviews = array_merge($allReviews, $reviews);
        }
        // Gán giá trị của $allReviews vào $resultByRate['all']
        $resultByRate['all'] = $allReviews;
        $count = $result->count();
        $resultByRate['total'] = $count;
        
        return json_encode([
            'status' => 200,
            'object' => $resultByRate
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review_Rating $review_Rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review_Rating $review_Rating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review_Rating $review_Rating)
    {
        //
    }
}
