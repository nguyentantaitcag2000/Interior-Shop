<script setup lang="ts">
import axios from 'axios';
import {onMounted, reactive,ref, watch} from 'vue';
import { shoppingCart,methodOfPayment,shipMethod, product } from '../../interface';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import { useRoute } from 'vue-router';
const route = useRoute();

interface FormState{
    fullName:string,
    address:string,
    phone:string,
    note:string,
    ID_MOP:number,
    ID_SM:number,
    ID_SC_List:number[],
    isShow:boolean,
    amountBuyNow: number | null,
    idProductBuyNow: number | null,
}
const form = reactive<FormState>({
    address:'',
    note:'',
    fullName:'',
    phone:'',
    ID_MOP:1,
    ID_SM:1,
    ID_SC_List : [], 
    isShow:false,
    amountBuyNow: null,
    idProductBuyNow:null,
});
const methodOfPaymentList = ref<methodOfPayment[]>();
const shipMethodList = ref<shipMethod[]>();
const checkoutFinished = ref(false);

const carts = ref<shoppingCart[]>();
const product = ref<product>();

const total_money = ref(0);
const total_money_with_vat = ref(total_money.value * 8 / 100) ;
const total_money_all = ref(total_money.value + total_money_with_vat.value);
const SubmitForm = (event:Event)=>{
    event.preventDefault();
    LazyCodet.AlertProcessing({
        requireConfirm: false,
        workerFunction() {
            
            form.isShow = isBuyNow();
            return axios.post('/api/order',form).then(res=>{
                if(res.data.status == 200)
                {
                    checkoutFinished.value = true;
                }
                else{
                    LazyCodet.AlertSuccess(res.data.message);
                }
            });        
        },
    })
    
}
const isBuyNow = ()=>{
    return route.params.id != '';
}
const calcMoney = ()=>{
    let total:number = 0;
    if(isBuyNow())
    {
        form.amountBuyNow = Number(route.params.amount); 
        form.idProductBuyNow = Number(route.params.id); 
        if (product.value && product.value.Price)
            total = Number(route.params.amount)  * product.value.Price;
    }
    else
    {
        carts.value?.forEach(function(val){
            total += val.cart_detail[0].Amount_CD * val.cart_detail[0].product.Price;
        });
    }
    
    total_money.value = total;
    total_money_with_vat.value = total_money.value * 8 / 100;
    total_money_all.value = total_money.value + total_money_with_vat.value;
}
onMounted(()=>{
    if(isBuyNow())
    {
        axios.get('/api/product/' + route.params.id).then(res=>{
            product.value = res.data;
            calcMoney();
        });
    }
    else{
        axios.get('/api/shoppingcart').then(res=>{
            carts.value = res.data.object;
            calcMoney();

            carts.value?.forEach(val=>{
                form.ID_SC_List.push(val.ID_SC);
            });
        });
    }
    axios.get('/api/method-of-payment').then(res=>{
        methodOfPaymentList.value = res.data;
    });
    axios.get('/api/ship-method').then(res=>{
        shipMethodList.value = res.data;
    });
    
    
});

</script>
<template>
    <div v-if="checkoutFinished" class="d-flex flex-column" style="height: 100vh;">
        <h4 class="text-center">Cảm ơn bạn đã đặt hàng, chúng tôi sẽ nhanh chóng giao tới cho bạn !</h4>
        <router-link to="/" class="btn btn-success ml-auto mr-auto" style="width: 200px;">Tiếp tục mua sắm</router-link>
        <img class="mt-3 ml-auto mr-auto" src="checkout-success.png" width="400" alt="">
    </div>
    <form v-else @submit="SubmitForm" method="POST" class="p-3 rounded" style="margin-bottom: 100px;">
        <div class="row">
            <div class="col-5 border-right p-3 bg-secondary">
                <h3>Thông tin thanh toán</h3>
                <div class="form-group ">
                    <label for="fname"><i class="fa fa-user"></i> Full Name</label>
                    <input v-model="form.fullName" type="text" class="form-control" id="fname" name="firstname" placeholder="Full Name" required>
                </div>
                
                <div class="form-group ">
                    <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
                    <input v-model="form.address" type="text" class="form-control" id="adr" name="address" placeholder="Address" required>
                </div>
                <div class="form-group ">
                    <label for="sdt"><i class="fa fa-phone"></i> Số điện thoại</label>
                    <input v-model="form.phone" type="text" class="form-control" id="sdt" name="sdt" placeholder="Số điện thoại" required>
                </div>
                <div class="form-group ">
                    <label for="exampleFormControlTextarea1">Ghi chú (nếu có)</label>
                    <textarea v-model="form.note" class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div>
                
            

            
                
            </div>
            <div class="col-7 pl-3">
                <h3><b>Phương thức thanh toán</b></h3>
                <div>
                    <div v-for="mop in methodOfPaymentList" class="form-check">
                        <input v-model="form.ID_MOP" class="form-check-input" :id="'mop' + mop.ID_MOP" type="radio" :value="mop.ID_MOP" name="gridRadios">
                        <label class="form-check-label" :for="'mop' + mop.ID_MOP">
                            {{ mop.Name_MOP }}
                        </label>
                    </div>
                </div>
                <h3><b>Phương thức vận chuyển</b></h3>
                <div>
                    <div v-for="mop in shipMethodList" class="form-check">
                        <input v-model="form.ID_SM" class="form-check-input" :id="'mop' + mop.ID_SM" type="radio" :value="mop.ID_SM" name="shipmethod">
                        <label class="form-check-label" :for="'mop' + mop.ID_SM">
                            {{ mop.Name_SM }}
                        </label>
                    </div>
                </div>
                
                <hr>
                <div>
                    <h4>Các sản phẩm: <span class="price" style="color:black"><i class="fa fa-shopping-cart"></i> <b>{{ carts?.length }}</b></span></h4>
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Tên sản phẩm</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Tổng tiền</th>
                            </tr>
                          
                        </thead>
                        <tbody>
                            <tr v-if="isBuyNow()">
                                <th scope="row">{{ product?.Name_Product }}</th>
                                <td>{{ route.params.amount }}</td>
                                <td>{{ LazyConvert.ToMoney(product?.Price) }}</td>
                            </tr>
                            <tr v-else v-for="ca in carts">
                                <th scope="row">{{ ca.cart_detail[0].product.Name_Product }}</th>
                                <td>{{ ca.cart_detail[0].Amount_CD }}</td>
                                <td>{{ LazyConvert.ToMoney(ca.cart_detail[0].product.Price) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <p>Tổng tiền đơn hàng <span class="price" style="color:black"><b>{{ LazyConvert.ToMoney(total_money) }}</b></span></p>
                    <p>VAT  <span class="price" style="color:black"><b>8%</b></span></p>
                    <p>Tổng phí VAT<span class="price" style="color:black"><b>&nbsp; {{ LazyConvert.ToMoney(total_money_with_vat) }}</b></span></p>
                    <p>Tổng tiền thanh toán<span class="price" style="color:rgba(0, 106, 255, 1)"><b>&nbsp; {{ LazyConvert.ToMoney(total_money_all) }}</b></span></p>
                    
                    <input type="text" name="price" value="<?=$data['total_money']?>" hidden>
                    <input type="text" name="total_money_checkout" value="<?=$data['total_money'] + $data['total_money'] * 8 / 100?>" hidden>
                    
                </div>
                
            </div>

            
        </div>
        <div class="d-flex justify-content-end m-3">
            <input type="submit" value="Đặt hàng" class="btn btn-primary ">    
        </div>
        
    </form>
</template>