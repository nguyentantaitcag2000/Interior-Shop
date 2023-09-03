<script setup lang="ts">
import axios from 'axios';
import { useRouter } from 'vue-router';
import {onMounted, reactive,ref, watch} from 'vue';
import { order } from '../../interface';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import TabMenu from 'primevue/tabmenu';
import { cart_tabs } from '../../tabs';
const order = ref<order[]>();
const router = useRouter();
const totalMoney = ref(0);
const events = ref([
    { status: 'Ordered', date: '15/10/2020 10:30', icon: 'pi pi-shopping-cart', color: '#9C27B0'},
    { status: 'Processing', date: '15/10/2020 14:00', icon: 'pi pi-cog', color: '#673AB7' },
    { status: 'Shipped', date: '15/10/2020 16:15', icon: 'pi pi-shopping-cart', color: '#FF9800' },
    { status: 'Delivered', date: '16/10/2020 10:00', icon: 'pi pi-check', color: '#607D8B' }
]);
onMounted(()=>{
    axios.get('/api/order/get').then(res=>{
        order.value = res.data;
        console.log(res.data);
    });
});

</script>
<template>
    <div class="p-3">
        <div>
            <h1 class="h1">Đơn hàng</h1>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
                <tr>
                <th>Đơn đặt hàng</th>
                <th>Ngày</th> 
                <th>Tình trạng</th>
                <th>Giao hàng đến</th>
                <th>Tổng</th>
                <th>Các thao tác</th>
                </tr>
            </thead>
            <tbody>
    
            <tr v-for="ord in order">
                <td>
                    <div class="form-check">
                    <label class="form-check-label" for="vehicle1">#{{ ord.bill.ID_Bill }}</label>
                    </div>
                </td>
                <td>{{ ord.bill.CreateDate }}</td>
                <td>{{ ord.bill.bill_status.Name_BS }}</td>
                <td>{{ ord.Address_O }}</td>
                <td>{{ LazyConvert.ToMoney(ord.bill.TotalMoneyCheckout)  }}</td>
                <td>
                        <a href="/Admin/CheckoutDone/<?=$bill['ID_Bill']?>" type="submit" class="btn btn-success">Đã thanh toán</a>    
                        <a href="/Admin/CheckoutNotDone/<?=$bill['ID_Bill']?>"  type="submit" class="btn btn-light">Chưa thanh toán</a>
                        <a href="/Admin/CheckoutShiping/<?=$bill['ID_Bill']?>"  type="submit" class="btn btn-primary">Đang vận chuyển</a>
                        <a href="/Admin/Destroy/<?=$bill['ID_Bill']?>"  type="submit" class="btn btn-danger">Hủy đơn</a>

                    
                </td>
            </tr>

                
            
            </tbody>
            </table>
            <a href="/Admin/Bill/1" class="btn btn-link">Xem các đơn chưa thanh toán</a>
            <a href="/Admin/Bill/2" class="btn btn-link">Xem các đơn đã thanh toán</a>
            <a href="/Admin/Bill/3" class="btn btn-link">Xem các đơn đang vận chuyển</a>
            <a href="/Admin/Bill/3" class="btn btn-link">Xem các đơn đã hủy</a>
            <a href="/Admin/Bill"   class="btn btn-link">Tất cả</a>
        </div>
    </div>
</template>