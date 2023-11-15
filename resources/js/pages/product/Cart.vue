<style>
.p-inputnumber-button{
    height: 20px!important;
}
.p-inputnumber{
    width: 3rem!important;
    height: 5rem!important;
}
</style>
<script setup lang="ts">
import axios from 'axios';
import { useRouter } from 'vue-router';
import {onMounted, reactive,ref, watch,computed} from 'vue';
import { shoppingCart } from '../../interface';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import TabMenu from 'primevue/tabmenu';
import { cart_tabs } from '../../tabs';
import InputNumber from 'primevue/inputnumber';
import Dropdown from "primevue/dropdown";
import Skeleton from 'primevue/skeleton';

interface shoppingCartCustom extends shoppingCart{
    ID_Color:number
}
const carts = ref<shoppingCartCustom[]>();
const router = useRouter();
const totalMoney = ref(0);
const colors = ref([]);
const calcMoney = ()=>{
    let total = 0;
    carts.value?.forEach(function(val){
        total += val.cart_detail[0].Amount_CD * val.cart_detail[0].product.Price;
    });
    totalMoney.value = total;
}
const selectedColor = computed(()=>{
});
const checkout = () => {
    //Lưu lại các thông tin như số lượng tăng giảm số lượng 
    LazyCodet.AlertProcessing({
        requireConfirm:false,
        processMessage:'Đang xử lý...',
        workerFunction: ()=>{
            return axios.post('/api/shoppingcart/updates',{carts: carts.value}).then(res=>{
                if(res.data.status == 200)
                {
                    router.push('/checkout');
                }
                else
                {
                    LazyCodet.AlertError(res.data.message);
                }
                
            });
        }
    });
}
const removeCart = (event:Event,cart:shoppingCartCustom)=>{
    event.preventDefault();
    LazyCodet.AlertProcessing({
        alertMessage: "Bạn có chắc chắn muốn xóa ?",
        workerFunction: ()=>{
            return axios.delete('/api/shoppingcart/remove/' + cart.ID_SC).then(res=>{
                if(res.data.status == 200)
                {
                    let indexDelete = carts.value?.indexOf(cart);
                    if(typeof indexDelete !== 'undefined')
                    {
                        carts.value?.splice(indexDelete,1);
                        setCountCard(carts.value?.length + '');
                    }
                }
                else
                {
                    LazyCodet.AlertError(res.data.message);
                }
                
            });
        }
    });
    
}
const isHaveColor  = (ca:shoppingCartCustom) => {
    if(ca.cart_detail[0].product.detail_product_color.length > 0)
    {
        if(ca.cart_detail[0].product.detail_product_color.length == 1)
        {
            if(ca.cart_detail[0].product.detail_product_color[0].ID_Color == 0)
                return false;
            return true;
        }
        return true;
    }
    return false;
}
watch(()=>carts, (oldVal, newVal) => {
    calcMoney();
}, { deep: true });
onMounted(()=>{
    axios.get('/api/shoppingcart').then(res=>{
        let result:shoppingCartCustom[]  = res.data.object;
        //Đem cái ID_Color ra ngoài cùng cấp với các thuộc tính của result
        let myarray = result.map(cart=>{
            return {
                ...cart,
                ID_Color: cart.cart_detail[0].ID_Color
            }
        }) as any;
        carts.value = myarray;
        calcMoney();
    });
    axios.get('/api/colors').then((res)=>{
        colors.value = res.data;
    });
});

</script>
<template>
    <div class="card">
        <TabMenu :model="cart_tabs" />
        <router-view />
    </div>
    <div class="p-3 bg-light mt-3 rounded" style="min-height: 100vh;">
        
        <form>
            <div v-if="carts" v-for="ca in carts">
                <input type="text" hidden/>
                <div class="row bg-light mt-3 w-100 border-bottom flex-grow-1">
                    <div class="col-sm-2">
                        <router-link :to="'/product/' + ca.cart_detail[0].product.ID_Product">
                            <img style="width: 100px;height: 100px;" :src="ca.cart_detail[0].product.Avatar" alt="" >    
                        </router-link>
                        
                    </div>
                    <div class="col-sm-4 d-flex align-items-center ">
                        <h5>{{ ca.cart_detail[0].product.Name_Product }}</h5>
                    </div>
                    <div v-if="isHaveColor(ca)" class="col-sm-2 d-flex align-items-center ">
                        <Dropdown v-model="ca.ID_Color" :options="ca.cart_detail[0].product.detail_product_color" optionValue="color.ID_Color" optionLabel="color.Name_Color" placeholder="Select a Color" class="w-full md:w-14rem" />
                    </div>
                    <div v-else class="col-sm-2 d-flex align-items-center "></div>
                    <div class="col-sm-2 d-flex align-items-center ">
                        <span class="origin">{{ LazyConvert.ToMoney(ca.cart_detail[0].product.Price) }}</span>
                    </div>
                    <div class="col-sm-1 d-flex align-items-center pb-4">
                        <div style="width:50%">
                            
                            <InputNumber v-model="ca.cart_detail[0].Amount_CD"  :min="1" showButtons buttonLayout="vertical" style="width: 3rem;"
            incrementButtonIcon="fas fa-plus" decrementButtonIcon="fas fa-minus" />
                        </div><!-- /input-group -->
                        <input type="text" class="product-total-money" value="<?= $value['Price'] * $value['Amount'] ?>" hidden  />

                    </div>
                    <div class="col-sm-1 d-flex align-items-center ">
                        <button @click="removeCart($event,ca)" class="btn btn-danger">Xoá</button>
                    </div>
                
                </div>
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
        </form>
        <div v-if="carts?.length == 0" class="d-flex flex-column">
            <h4 class="text-center">Giỏ hàng đang trống, Nào ! hãy đặt chọn và đặt vào giỏ hàng ngay.</h4>
            <router-link to="/" class="btn btn-primary m-auto">OK</router-link>
        </div>
        <div v-else class="row bottom-0 bg-light flex-grow-1 container p-3">
            <div class="col-sm-3">
                <span>Tổng thanh toán:</span>
                <span id="total_money">{{ LazyConvert.ToMoney(totalMoney) }}</span>
            </div>
            <div class="col-sm-1,2">
                <a class="btn btn-success" @click="checkout">Thanh toán</a>
            </div>
        </div>    
    </div>
</template>