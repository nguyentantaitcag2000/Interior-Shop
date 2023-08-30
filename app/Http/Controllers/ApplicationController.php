<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function admin()
    {
        return view('admin.layouts.app');
    }
    public function shop()
    {
        return view('shop.layouts.app');
    }
    public function auth()
    {
        return view('auth.layouts.app');
    }
    public function logout()
    {
        session()->remove('email');
        return redirect('/');
    }
    
}
