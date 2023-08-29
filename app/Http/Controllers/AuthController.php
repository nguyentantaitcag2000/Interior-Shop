<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function signin (Request $request)
    {
        // Lấy thông tin từ request
        $email = $request->input('email');
        $mat_khau = $request->input('password');
        
        // Kiểm tra thông tin đăng nhập
        $khach_hang = User::where('Email', $email)->first();
        if (!$khach_hang) {
            return json_encode([
                'status' => 401,
                'message' => 'Email không chính xác.'
            ]);
        }
        if (!Hash::check($mat_khau, $khach_hang->password)) {
            return json_encode([
                'status' => 401,
                'message' => 'Mật khẩu không chính xác.'
            ]);
        }
        // Đăng nhập thành công, tạo token cho người dùng
        // $token = $khach_hang->createToken('access_token')->plainTextToken;

        // $request->session()->put('email', $email);
        session(['email' => $email]);

        return json_encode([
            'status' => 200,
            'message' => 'Đăng nhập thành công!'
        ]);
    }
}
