<script setup lang="ts">
import axios from 'axios';
import { useRouter } from 'vue-router';
import {onMounted, reactive,ref, watch} from 'vue';
import { order } from '../../interface';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import TabMenu from 'primevue/tabmenu';
import { cart_tabs } from '../../tabs';
import Skeleton from 'primevue/skeleton';

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
    axios.post('/api/order/get',{ID_BS:1}).then(res=>{
        order.value = res.data;
        console.log(res.data);
    });
});

</script>
<template>
    <div class="card">
        <TabMenu :model="cart_tabs" />
        <router-view />
    </div>
    <div class="p-3 bg-light mt-3 rounded" style="min-height: 100vh;">
        
        <div v-if="order" v-for="ord in order">
            <div style="background-color: rgb(214, 214, 214);" class="p-3 mt-3 d-flex justify-content-between rounded">
                <router-link :to="'/bill/' + ord.bill.ID_Bill">Đơn hàng #{{ ord.bill.ID_Bill }}</router-link>
                <span>{{ ord.bill.CreateDate }}</span>
            </div>
            <form class="ml-5">
                <div v-for="ord_detail in ord.order_detail">
                    <div v-for="car_detail in ord_detail.shopping_cart.cart_detail">
                        <input type="text" hidden/>
                        <div class="row bg-light mt-3 w-100 border-bottom flex-grow-1">
                            <div class="col-sm-2">
                                <router-link :to="'/product/' + car_detail.product.ID_Product">
                                    <img style="width: 100px;height: 100px;" :src="car_detail.product.Avatar" alt="" >    
                                </router-link>
                                
                            </div>
                            <div class="col-sm-4 d-flex align-items-center ">
                                <h5>{{ car_detail.product.Name_Product }}</h5>
                            </div>
                            <div class="col-sm-2 d-flex align-items-center ">
                                <span class="origin">{{ LazyConvert.ToMoney(car_detail.product.Price) }}</span>
                            </div>
                            <div class="col-sm-2 d-flex align-items-center ">
                                x{{ car_detail.Amount_CD }}
                            </div>
                        
                        
                        </div>
                    </div>
                    
                </div>
                
            </form>

        </div>
        <div v-else class="border-round border-1 surface-border p-4 surface-card">
                <ul class="m-0 p-0 list-none">
                    <li class="mb-3">
                        <div class="flex">
                            <Skeleton shape="circle" size="4rem" class="mr-2"></Skeleton>
                            <div class="align-self-center" style="flex: 1">
                                <Skeleton width="100%" class="mb-2"></Skeleton>
                                <Skeleton width="75%"></Skeleton>
                            </div>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="flex">
                            <Skeleton shape="circle" size="4rem" class="mr-2"></Skeleton>
                            <div class="align-self-center" style="flex: 1">
                                <Skeleton width="100%" class="mb-2"></Skeleton>
                                <Skeleton width="75%"></Skeleton>
                            </div>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="flex">
                            <Skeleton shape="circle" size="4rem" class="mr-2"></Skeleton>
                            <div class="align-self-center" style="flex: 1">
                                <Skeleton width="100%" class="mb-2"></Skeleton>
                                <Skeleton width="75%"></Skeleton>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="flex">
                            <Skeleton shape="circle" size="4rem" class="mr-2"></Skeleton>
                            <div class="align-self-center" style="flex: 1">
                                <Skeleton width="100%" class="mb-2"></Skeleton>
                                <Skeleton width="75%"></Skeleton>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

        
  
    </div>
</template>