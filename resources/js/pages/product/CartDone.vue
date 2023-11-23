<script setup lang="ts">
import axios from 'axios';
import { useRouter } from 'vue-router';
import {onMounted, reactive,ref, watch} from 'vue';
import { cartDetail, order } from '../../interface';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import TabMenu from 'primevue/tabmenu';
import { cart_tabs } from '../../tabs';
import Skeleton from 'primevue/skeleton';
import Badge from 'primevue/badge';

import Rating from 'primevue/rating';

import Textarea from 'primevue/textarea';

import Dialog from 'primevue/dialog';

import Button from 'primevue/button';

const contentRating = ref('');
const order = ref<order[]>();
const router = useRouter();
const totalMoney = ref(0);
const events = ref([
    { status: 'Ordered', date: '15/10/2020 10:30', icon: 'pi pi-shopping-cart', color: '#9C27B0'},
    { status: 'Processing', date: '15/10/2020 14:00', icon: 'pi pi-cog', color: '#673AB7' },
    { status: 'Shipped', date: '15/10/2020 16:15', icon: 'pi pi-shopping-cart', color: '#FF9800' },
    { status: 'Delivered', date: '16/10/2020 10:00', icon: 'pi pi-check', color: '#607D8B' }
]);
const visible = ref(false);
const rating = ref(5);
const ratingFinished = ref(false);
const id_user = (document.getElementById('id_user_login') as HTMLInputElement).value;
const isRatingProgress = ref(false);
const selectedCartRating = ref<cartDetail>();
function DanhGia()
{
    isRatingProgress.value = true;
    axios.post('/api/rating',{
            content: contentRating.value,
            value: rating.value,
            idProduct: selectedCartRating!.value?.ID_Product,
            idUser: id_user,
        }).then(res=>{
        if(res.data.status == 200)
        {
            isRatingProgress.value = false;
            LazyCodet.AlertSuccess(res.data.message);
            ratingFinished.value = true;
        }
        else
        {
            LazyCodet.AlertError(res.data.message);
        }
    });
          
}
onMounted(()=>{
    axios.post('/api/order/get',{ID_BS:2}).then(res=>{
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
                <div>
                    <router-link :to="'/bill/' + ord.bill.ID_Bill">Đơn hàng #{{ ord.bill.ID_Bill }}</router-link>
                    <Badge :value="ord.bill.bill_status.Name_BS" class="ml-3"></Badge>
                    <Badge v-if="ord.order_detail[0].shopping_cart.cart_detail.length == 0" value="Sản phẩm đã bị xoá" severity="danger" class="ml-3"></Badge>
                    
                </div>
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
                            <div class="col-sm-2 d-flex ">
                                <button type="button" @click="visible = true; selectedCartRating = car_detail" class="btn btn-success ml-3">⭐ Đánh giá</button>
                                <Dialog v-model:visible="visible" header="Đánh giá sản phẩm" :style="{ width: '50rem' }"
                                :pt="{
                                    mask: {
                                        style: 'backdrop-filter: blur(2px)'
                                    }
                                }"
                                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
                                    <div v-if="ratingFinished">
                                        <h2><b>Cảm ơn bạn đã cho chung tôi biết cảm xúc của bạn về sản phẩm ^^</b></h2>
                                       
                                        <div class="d-flex align-items-center">
                                            <img src="/shy-hmmm.gif"/>
                                            <div>
                                                <span class="h3">Bạn có muốn xem tiếp các sản phẩm khác không ?</span><br><br>
                                                <button class="btn btn-primary">
                                                    <router-link to="/">Oki</router-link>
                                                </button>
                                                <button @click="visible = false" class="btn btn-dark border ml-3">Không</button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div v-else>
                                        <h2><b>Bạn cảm thấy sản phẩm đã mua như thế nào ?</b></h2>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <router-link :to="'/product/' + selectedCartRating!.product.ID_Product">
                                                    <img style="width: 100px;height: 100px;" :src="selectedCartRating!.product.Avatar" alt="" >    
                                                </router-link>
                                                
                                            </div>
                                            <div class="col-sm-4 d-flex align-items-center ">
                                                <h5>{{ selectedCartRating!.product.Name_Product }}</h5>
                                            </div>
                                        </div>
                                        <hr>
                                        <h3 class="text-center">Hãy cho chúng tôi biết mức độ hài lòng của bạn nhé !</h3>
                                        <Rating v-model="rating" class="d-flex justify-content-center p-3" :cancel="false">
                                            <template #cancelicon>
                                                <img src="/cancel.png" height="24" width="24" />
                                            </template>
                                            <template #onicon>
                                                <img src="/custom-onicon.png" height="24" width="24" />
                                            </template>
                                            <template #officon>
                                                <img src="/custom-officon.png" height="24" width="24" />
                                            </template>
                                        </Rating>
                                        <div class="d-flex justify-content-center mt-4">
                                            <div>
                                                <span class="p-float-label">
                                                    <Textarea v-model="contentRating" rows="5" cols="30" />
                                                    <label>Nội dung đánh giá</label>
                                                </span>
                                            </div>
                                            
                                            <div class="ml-2">
                                                <span>Gợi ý:</span><br>
                                                <button @click="contentRating='Sản phẩm rất tốt'; rating = 5" class="btn btn-dark border ml-1">Sản phẩm rất tốt</button>
                                                <button @click="contentRating='Không hài lòng lắm'; rating = 3" class="btn btn-dark ml-1">Không hài lòng</button>
                                                <button @click="contentRating='Tệ'; rating = 1" class="btn btn-dark ml-1">Tệ</button>
                                            </div>
                                        
                                        </div>
                                        
                                        <div class="d-flex justify-content-center mt-4">
                                            <Button type="button" label="Đánh giá" :loading="isRatingProgress" @click="DanhGia()" />

                                        </div>
                                    </div>
                                    
                                    
                                </Dialog>
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