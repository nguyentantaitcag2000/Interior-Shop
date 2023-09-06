<script setup lang="ts">
import axios from 'axios';
import {onMounted, ref, reactive} from 'vue'
import {users,product,bill} from '../interface';

interface Inventory{
    products:product[],
    totalAmount: number;
}
const users = ref<users[]>();
const products = ref<product[]>();
const bills = ref<bill[]>();
const inventory = ref<Inventory>({
    products: [],
    totalAmount: 0

});
const InventoryExport = ()=>{
    axios({
        url: '/export',
        method: 'GET',
        responseType: 'blob', // quan trọng để xử lý dữ liệu nhị phân
    }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'products.xlsx');
        document.body.appendChild(link);
        link.click();
    });

}
onMounted(()=>{
    axios.get('/api/users').then(res=>{
        users.value = res.data;
    });
    axios.get('/api/products/_/_').then(res=>{
        products.value = res.data;
    });
    axios.get('/api/bill/processing').then(res=>{
        bills.value = res.data;
    });
    axios.get('/api/products/inventory').then(res=>{
        inventory.value = res.data;
    });
});
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tổng quan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Starter Page</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-info p-2">
                        <div class="inner">
                            <h3 id="total_users">{{ users?.length }}</h3>
                            <p>Tổng khách hàng</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-primary p-2">
                        <div class="inner">
                            <h3 id="total_users">{{ products?.length }}</h3>
                            <p>Tổng sản phẩm</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-table"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-warning p-2">
                        <div class="inner">
                            <h3 id="total_users">{{ bills?.length }}</h3>

                            <div class="d-flex">
                                <p>Đơn hàng cần xử lý</p>
                                <router-link to="/admin/bill/1" class="btn btn-dark ml-3">Xem</router-link>
                            </div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-snowboarding"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-secondary p-2">
                        <div class="inner">
                            <h3 id="total_users">{{ inventory.totalAmount }}</h3>
                            <div class="d-flex">
                                <p>Tổng sản phẩm tồn kho</p>
                                <button @click="InventoryExport" class="btn btn-light ml-3">Excel Donwload</button>
                            </div>

                        </div>
                        <div class="icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>


            </div>
            <h1>Top các sản phẩm được mua nhiều nhất</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Tổng số lượng sản phẩm được bán</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <?php foreach ($data['Top10SanPhamDuocMuaNhieuNhat'] as $sanpham) { ?>
                    <tr>
                        <td><?php echo $sanpham['Name_Product']; ?></td>
                        <td><?php echo $sanpham['TongSoLuong']; ?></td>
                    </tr>
                    <?php } ?> -->
                </tbody>
            </table>
        </div>
    </div>
</template>

