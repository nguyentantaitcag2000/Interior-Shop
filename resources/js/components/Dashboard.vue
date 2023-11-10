<script setup lang="ts">
import axios from 'axios';
import {onMounted, ref, reactive} from 'vue'
import {users,product,bill} from '../interface';
import Dropdown from 'primevue/dropdown';
import Chart from 'primevue/chart';

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
const FilterOptions = ref([
    { name: 'Toàn bộ thời gian', code: 'A' },
    { name: '1 tháng', code: '1' },
    { name: '3 tháng', code: '3' },
    { name: '6 tháng', code: '6' },
    { name: '1 năm', code: '12' }
]);
const selectedFilter = ref(FilterOptions.value[0]);

const tongDoanhSo = ref(-1);
const top10 = ref<product[]>([]);

const chartData = ref();
const chartOptions = ref();
const chartData_SoLuongNguoiDatHang = ref();
const chartOptions_SoLuongNguoiDatHang = ref();
const chartDataFromAPI = ref([]);
const chartDataFromAPI_SoLuongNguoiDatHang = ref([]);
const setChartData = () => {
    let ngayOrThangOrNam = "";
    if(selectedFilter.value.code == "1")
        ngayOrThangOrNam = "Ngày ";
    else if(selectedFilter.value.code == "A")
        ngayOrThangOrNam = "Năm ";
    else
        ngayOrThangOrNam = "Tháng ";
    

    const labels = Object.keys(chartDataFromAPI.value).map(key => ngayOrThangOrNam + (parseInt(key) + 1));
    return {

        labels: labels,
        datasets: [
            {
                label: 'Doanh số',
                data: Object.values(chartDataFromAPI.value),
                backgroundColor: ['rgba(255, 159, 64, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(153, 102, 255, 0.2)'],
                borderColor: ['rgb(255, 159, 64)', 'rgb(75, 192, 192)', 'rgb(54, 162, 235)', 'rgb(153, 102, 255)'],
                borderWidth: 1
            }
        ]
    };
};
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--text-color-secondary');
    const surfaceBorder = documentStyle.getPropertyValue('--surface-border');

    return {
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
}


const setChartData_SoLuongNguoiDatHang = () => {
    let ngayOrThangOrNam = "";
    if(selectedFilter.value.code == "1")
        ngayOrThangOrNam = "Ngày ";
    else if(selectedFilter.value.code == "A")
        ngayOrThangOrNam = "Năm ";
    else
        ngayOrThangOrNam = "Tháng ";
    

    const labels = Object.keys(chartDataFromAPI_SoLuongNguoiDatHang.value).map(key => ngayOrThangOrNam + (parseInt(key) + 1));
    return {

        labels: labels,
        datasets: [
            {
                label: 'Số lần/người đặt',
                data: Object.values(chartDataFromAPI_SoLuongNguoiDatHang.value),
                backgroundColor: ['rgba(255, 159, 64, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(153, 102, 255, 0.2)'],
                borderColor: ['rgb(255, 159, 64)', 'rgb(75, 192, 192)', 'rgb(54, 162, 235)', 'rgb(153, 102, 255)'],
                borderWidth: 1
            }
        ]
    };
};
// const setChartOptions_SoLuongNguoiDatHang = () => {
//     const documentStyle = getComputedStyle(document.documentElement);
//     const textColor = documentStyle.getPropertyValue('--text-color');
//     const textColorSecondary = documentStyle.getPropertyValue('--text-color-secondary');
//     const surfaceBorder = documentStyle.getPropertyValue('--surface-border');

//     return {
//         plugins: {
//             legend: {
//                 labels: {
//                     color: textColor
//                 }
//             }
//         },
//         scales: {
//             x: {
//                 ticks: {
//                     color: textColorSecondary
//                 },
//                 grid: {
//                     color: surfaceBorder
//                 }
//             },
//             y: {
//                 beginAtZero: true,
//                 ticks: {
//                     color: textColorSecondary
//                 },
//                 grid: {
//                     color: surfaceBorder
//                 }
//             }
//         }
//     };
// }
const setChartOptions_SoLuongNguoiDatHang = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--text-color-secondary');
    const surfaceBorder = documentStyle.getPropertyValue('--surface-border');

    // Mảng chứa các giá trị số nguyên duy nhất
    const uniqueIntegers = Array.from({ length: 10 }, (_, i) => i);

    return {
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: textColorSecondary,
                    callback: function (value:any, index:any, values:any) {
                        // Chỉ hiển thị giá trị từ mảng uniqueIntegers
                        return uniqueIntegers.includes(value) ? value.toString() : '';
                    }
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
};
function ChooseFilter()
{
    tongDoanhSo.value = -1;
    axios.post('/api/product/tongDoanhSo', {timeCode: selectedFilter.value.code }).then(res=>{
        const resDataObject: Record<string, number> = res.data;

        tongDoanhSo.value = res.data;

        chartDataFromAPI.value = res.data;
        tongDoanhSo.value = 0
        Object.values(resDataObject).forEach((value: number) => {
            tongDoanhSo.value += value;
        });
        chartData.value = setChartData();
        chartOptions.value = setChartOptions();
    });
    axios.post('/api/product/tongNguoiDatHang', {timeCode: selectedFilter.value.code }).then(res=>{
        chartDataFromAPI_SoLuongNguoiDatHang.value = res.data;
      
        chartData_SoLuongNguoiDatHang.value = setChartData_SoLuongNguoiDatHang();
        chartOptions_SoLuongNguoiDatHang.value = setChartOptions_SoLuongNguoiDatHang();
    });
}
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
    axios.post('/api/products/top10').then(res=>{
        top10.value = res.data;
    });
    ChooseFilter();
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
            <div class="row" >
                <div>
                    Lọc theo 
                    <Dropdown v-model="selectedFilter" @change="ChooseFilter" :options="FilterOptions" optionLabel="name" placeholder="Chọn 1 tùy chọn" class="w-full md:w-14rem" />
                </div>
                
            </div>
            <br>
            <div class="row">
                
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-secondary p-2">
                        <div class="inner">
                            <h3 v-if="tongDoanhSo == -1">Loading...</h3>
                            <h3 v-else>{{ tongDoanhSo.toLocaleString('it-IT', {style : 'currency', currency : 'VND'}) }}</h3>
                            <div class="d-flex">
                                <p>Doanh số</p>
                            </div>

                        </div>
                        <div class="icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
                <!-- Biểu đồ doanh số -->
                <Chart class="w-100" type="bar" :data="chartData" :options="chartOptions" />
                <!-- Biểu đồ số lượng người mua đặt hàng -->
                <h1>Số lượng người mua đặt hàng</h1>
                <Chart class="w-100" type="line" :data="chartData_SoLuongNguoiDatHang" :options="chartOptions_SoLuongNguoiDatHang" />
            </div>
            <h1>Top các sản phẩm được mua gần đây</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Tên sản phẩm</th>
                        <th>Mã sản phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <tr v-for="(sp, index) in top10" :key="index" >
                        <td>{{ index + 1 }}</td>
                        <td>{{ sp.Name_Product }}</td>
                        <td>#{{ sp.ID_Product }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

