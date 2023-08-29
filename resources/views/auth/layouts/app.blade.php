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
            </div>
            
        </nav>
        <hr>
        
        <div class="container bg-light">
            <router-view></router-view>
        </div>
        
       
    </div>
</body>

</html>