<script setup lang="ts">
import axios from 'axios';
import { useRoute } from 'vue-router';
import {onMounted, reactive,ref, watch} from 'vue';
import { billFull,cartDetail } from '../../interface';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import TabMenu from 'primevue/tabmenu';
import { cart_tabs } from '../../tabs';
const bill = ref<billFull>();
const route = useRoute();
const formatCurrency = (value:any) => {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    }
const priceTemplate = (rowData:any) => {
    return formatCurrency(rowData.product.Price);
}
const totalPriceTemplate = (rowData:any) => {
    return formatCurrency(rowData.product.Price * rowData.Amount_CD);
}
const imageTemplate = (rowData:any) => {
      return `<img src="${rowData.product.Avatar}" alt="${rowData.product.Name_Product}" class="product-image" />`;
    }
const flattenedCartDetails = (): cartDetail[] => {
  if (!bill.value?.order?.order_detail) return [];

  return bill.value.order.order_detail.reduce<cartDetail[]>((acc, orderDetail) => {
    return acc.concat(orderDetail.shopping_cart.cart_detail);
  }, []);
}
onMounted(()=>{
    axios.get('/api/bill/' + route.params.id).then(res=>{
        bill.value = res.data;
        console.log(res.data);
       

    });
});

</script>
<template>
    <div class="p-3">
        <!-- Header và thông tin khác của hóa đơn -->
        <h3 class="text-center">Công ty Cổ phần bán lẻ LazyCodet</h3>
        <div class="d-flex justify-content-center">
            <img class="rounded-circle" src="/icon-bill.jpg" width="50" height="50">
        </div>
        <hr class="w-50 mx-auto">
        <h1 class="text-center text-bold">Hóa đơn</h1>
        <p class="p-1"><b>Đơn hàng:</b> #{{ bill?.ID_Bill }}</p>
        <p class="p-1"><b>Số phiếu:</b> #{{ bill?.order.ID_Order }}</p>
        <p class="p-1"><b>Ngày tạo hóa đơn:</b> {{ bill?.CreateDate }}</p>
        <p class="p-1"><b>Khách hàng:</b> {{ bill?.order.Customer_Name }}</p>
        <p class="p-1"><b>Địa chỉ giao hàng:</b> {{ bill?.order.Address_O   }}</p>
        <p class="p-1"><b>Ghi chú:</b> {{ bill?.order.Note_O   }}</p>
        <p class="p-1"><b>Số điện thoại:</b> {{ bill?.order.Phone_O   }}</p>
        <p class="p-1"><b>Hình thức thanh toán:</b> {{ bill?.order.methodofpayment.Name_MOP   }}</p>
        <!-- Bảng chi tiết giỏ hàng -->
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Tên sản phẩm</th>
                    <th scope="col">Chất liệu</th>
                    <th scope="col">Màu sắc</th>
                    <th scope="col">Số lượng</th>
                    <th scope="col">Tổng tiền</th>
                </tr>
                
            </thead>
            <tbody>
                <tr v-for="ca in bill?.order.order_detail">
                    <template  v-for="ca_de in ca.shopping_cart.cart_detail">
                        <th scope="row">{{ ca_de.product.Name_Product}}</th>
                        <td>{{ ca_de.material != null ?  ca_de.material.Name_Material : '' }}</td>
                        <td>{{ ca_de.color != null ? ca_de.color.Name_Color : '' }}</td>
                        <td>{{ ca_de.Amount_CD }}</td>
                        <td>{{ LazyConvert.ToMoney(ca_de.product.Price) }}</td>
                    </template >
                    
                </tr>
            </tbody>
        </table>
        <!-- Tổng tiền -->
        <div class="total">
        <h6>Tổng tiền đơn hàng: {{ formatCurrency(bill?.TotalMoney) }}</h6>
        <h6>VAT: {{ formatCurrency(bill?.VAT_rate) }}</h6>
        <h6>Phí VAT: {{ formatCurrency(bill?.VAT_amount) }}</h6>
        <h4>Tổng tiền thanh toán: {{ formatCurrency(bill?.TotalMoneyCheckout) }}</h4>
        <!-- Các thông tin khác -->
        </div>
  </div>
</template>