<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
class ImageController extends Controller
{
    public function getImage($filename)
    {
        $imageContent = file_get_contents(public_path($filename));

        $response = Response::make($imageContent);
        $response->header('Content-Type', 'image/jpeg');
        $response->header('Cache-Control', 'public, max-age=86400'); // 86400 giây = 1 ngày

        return $response;
    }
}
