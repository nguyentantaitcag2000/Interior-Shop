<?php
use Illuminate\Support\Facades\Session;
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Starter</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="{{asset('css/w3.css')}}">
    <link rel="stylesheet" href="{{asset('css/grid-product.css')}}">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-sm w3-animate-top bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">
                    <img src="/images/icon.png" class="rounded-circle" width="50" height="50">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                <i class="fa fa-bars" style="font-size:24px;color:black"></i>
                </button>
                <div class="collapse navbar-collapse" id="mynavbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                        <a class="nav-link text-dark" href="/Home/Introduce">Giới thiệu</a>
                        </li>
                        
                        <li class="nav-item">
                        <a class="nav-link text-dark" href="/Home/Contact">Liên hệ</a>
                        </li>
                    </ul>
                    <div class="d-flex position-relative" id="search_bar">
                        <input id="search" class="form-control me-2" type="text" style="height: 50px;" placeholder="Tìm kiếm thứ gì đó">
                        <ul id="search-results" class="list-group" style="top:40px"></ul>

                        <button id="btn_search" class="btn btn-primary" type="button">Search</button>
                    </div>
                
                </div>
                <div class="d-flex justify-content-end">

                    <?php if(session()->has('email') == false) {?>
                        <div>
                                <a href="/auth/signin" class="btn btn-success">Sign in</a>
                                <a href="/auth/signup" class="btn btn-danger">Sign up</a>
                        </div>
                        <?php }?>
                        
                        <div class="position-relative btn hover">
                            <span id='count_product_in_cart' style="width:25px;height:25px;top: -10px;right: 4px;" class="position-absolute d-block bg-danger text-light rounded-circle text-center" style="right: 15px;top: -13px;">9</span>
                            <a href="/Home/Cart">
                                <img src="/images/shopping-cart.png" class="me-3" style="width:35px;padding: 0;">  
                            </a>
                        </div>
                        <?php if(session()->has('email')) {?>
                        <div class="btn dropdown">
                            <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-expanded="false">
                            <?=session('email');?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item " href="/auth/logout">Đăng xuất</a></li>
                            </ul>
                            <img src="./public/images/profile.png" style="width:35px">
                        </div>
                    <?php }?>    
                </div>
            </div>
            
        </nav>
        <hr>
        
        <div class="container bg-light">
            <router-view></router-view>
        </div>
        
        <footer class="w3-animate-opacity">
        <div class="container">
            <div class="row">
            <div class="col-md-4">
                <h3>Về chúng tôi</h3>
                <p>Chúng tôi cung cấp các sản phẩm trang trí nội thất chất lượng cao với giá cả hợp lý. Chúng tôi cam kết mang lại sự hài lòng cho khách hàng.</p>
            </div>
            <div class="col-md-4">
                <h3>Liên hệ</h3>
                <p>Địa chỉ: Số 123, đường ABC, quận XYZ, TP. Cần Thơ</p>
                <p>Điện thoại: 0123456789</p>
                <p>Email: info@example.com</p>
            </div>
            <div class="col-md-4">
                <h3>Theo dõi chúng tôi</h3>
                <ul class="list-inline social-media">

                <li><a href="#"><i class="fa-brands fa-facebook"></i>&nbsp;&nbsp;&nbsp;Facebook</a></li>
                <li><a href="#"><i class="fa fa-twitter"></i>&nbsp;&nbsp;&nbsp;Twitter</a></li>
                <li><a href="#"><i class="fa fa-instagram"></i>&nbsp;&nbsp;&nbsp;Instagram</a></li>
                </ul>
            </div>
            </div>
        </div>
        </footer>
    </div>
</body>

</html>