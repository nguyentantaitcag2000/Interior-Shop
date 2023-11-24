<template>
    <div class="p-5">
    <section >
        <div class="introduce__recruit--center">
            <h1 class="recruit-title">LIÊN HỆ</h1>
            <p class="recruit-des">Luôn lắng nghe ý kiến từ khách hàng</p>
        </div>
    </section>
    <section class="contact-us">
        <article class="map">
            <h4 class="map__tile"><b>THÔNG TIN LIÊN HỆ</b></h4>
            <div class="map__street">
                <i class="fas fa-map-marker-alt map-icon"></i>
                <span class="map-address ml-2">Phường Xuân Khách, Quận Ninh Kiều, TP Cần Thơ</span>
            </div>
            <div class="map__phone">
                <i class="fas fa-phone-alt phone-icon"></i>
                <span class="number-phone ml-2">0342 123 123</span>
            </div>
            <img src="../../../public/images/products/product64/product64_main.jpg">
        </article>
        <section class="container mt-5">
            <form class="formLienHe">
                <h2 class="form__title"><b>GỬI TIN NHẮN CHO CHÚNG TÔI</b></h2>
                <div class="form-group">
                    <input id="txt-name" type="text" class="form-control" placeholder="Họ tên (*)">
                </div>
                <div class="form-group">
                    <input id="txt-phone" type="text" class="form-control" placeholder="Số điện thoại (*)">
                </div>
                <div class="form-group">
                    <input id="txt-email" type="email" class="form-control" placeholder="Email">
                </div>
                <div class="form-group">
                    <textarea id="txt-message" cols="30" rows="10" class="form-control" placeholder="Nội dung"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">GỬI TIN NHẮN</button>
            </form>
        </section>
    </section>
    </div>
</template>