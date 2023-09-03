<style>
.p-galleria-thumbnail-item{
    overflow-x: hidden !important;
    
}
img {
    object-fit: cover !important;
}
#product-avt{
    width: 100%!important;
}
</style>
<script setup lang="ts">
import {onMounted, ref,reactive,computed} from 'vue';
import { useRoute } from 'vue-router';
import { product } from '../../interface';
import axios, { formToJSON } from 'axios';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import Galleria from 'primevue/Galleria';
import Image from 'primevue/image';
import Rating from 'primevue/rating';
const route = useRoute();
const myProduct = ref<product>();
const activeIndex = ref(1);
const rating = ref(0);
interface MyImage {
    itemImageSrc: string;
    thumbnailImageSrc: string;
    alt: string;
}

const images = ref<MyImage[]>([]);
interface FormState{
    ID_Product: string,
    Amount: number
}
const responsiveOptions = ref([
    {
        breakpoint: '991px',
        numVisible: 4
    },
    {
        breakpoint: '767px',
        numVisible: 3
    },
    {
        breakpoint: '575px',
        numVisible: 1
    }
]);
const visibleCount = computed(() => {
    return images.value.length > 3 ? 3 : images.value.length;
});
const form = reactive<FormState>({
    ID_Product: route.params.id as string,
    Amount: 1
})
const changeAmount = (event:Event) => {
    let button = event.target as HTMLElement;

    if(button.className.includes('btn-minuse'))
        form.Amount = form.Amount > 1 ? form.Amount -1 : form.Amount;
    else
        form.Amount++;
}

const AddCart = ()=>{
    LazyCodet.AlertProcessing({
        requireConfirm: false,
        workerFunction() {
            return axios.post('/api/shoppingcart/insert',form).then(res=>{
                if(res.data.status == 200)
                {
                    LazyCodet.AlertSuccess('Đã thêm vào giỏ hàng');
                    setCountCard(res.data.object.length);
                }
                else
                {
                    LazyCodet.AlertError(res.data.message);
                }
            });
        },
    });
    
}
onMounted(()=>{
    axios.get('/api/product/' + route.params.id).then(res => {
        myProduct.value = res.data;

        if (myProduct.value?.Avatar) {
            images.value.push({
                itemImageSrc: myProduct.value.Avatar,
                thumbnailImageSrc: myProduct.value.Avatar,
                alt: "Avatar Image"
            });
        }

        if (myProduct.value?.detail_product_image) {
            myProduct.value.detail_product_image.forEach(element => {
                if (element.Image) {
                    images.value.push({
                        itemImageSrc: element.Image,
                        thumbnailImageSrc: element.Image,
                        alt: "Detail Image"
                    });
                }
            });
        }
    });
    axios.post('/api/shoppingcart/get_carts_not_checkout').then(res => {
        let array = res.data;
        if(array.length > 0 )
        {
            setCountCard(array.length);
        }
    });
});
</script>
<template>
    <div class="container p-5 mt-5 bg-light" style="border-radius: 25px;">
        <div class="row w-100">
            <div class="col-lg-5">
                <div class="p-3">
                        <Galleria :value="images" :responsiveOptions="responsiveOptions" :numVisible="visibleCount" containerStyle="max-width: 640px">
                            <template #item="slotProps">
                                <Image :src="slotProps.item.itemImageSrc" height="400"  :alt="slotProps.item.alt" :id="'product-avt'"  preview />
                            </template>
                            <template #thumbnail="slotProps">
                                <img :src="slotProps.item.thumbnailImageSrc" width="100" height="100" :alt="slotProps.item.alt" />
                            </template>
                        </Galleria>
                    
                    <!-- <div class="row border">
                        <div class="d-flex justify-content-center w3-animate-zoom">
                            <img :src="myProduct?.Avatar" style="width: 100%;">
                        </div>
                        <div class="d-flex justify-content-around" style="width: 100%;">
                            <img v-for="img in myProduct?.detail_product_image.slice(0,3)" width="100" class="border w3-animate-zoom" :src="img.Image">
                        </div>
                        
                    </div>	 -->
                </div>
            </div>
            <div class="col-lg-7 w3-animate-bottom">
                <div class="pl-3 ml-3">
                    <input value="<?=$data['Product']['ID_Product']?>" id="product_id" hidden>
                    <h1>{{ myProduct?.Name_Product }}</h1>
                    <p class="text-danger h3"><strong>{{ LazyConvert.ToMoney(myProduct?.Price) }}</strong></p>
                    <div>
                        <span class="d-block mt-1"><b>Màu sắc:</b> {{ myProduct?.detail_product_color.map(item => item.color.Name_Color).join(', ') }}</span><br>
                        <span class="d-block mt-1"><b>Kích thước:</b> {{ myProduct?.Size }}</span><br>
                        <span class="d-block mt-1"><b>Chất liệu:</b> {{ myProduct?.detail_product_material.map(item=> item.material.Name_Material).join(', ') }}</span><br>
                        <div class="row g-3 align-items-center mt-1">
                            <div class="col-auto">
                                <label for="inputPassword6" class="col-form-label">Số lượng</label>
                            </div>
                            <div class="col-auto">
                                <div class="input-group" style="width:50%">
                                    <span class="input-group-btn">
                                        <button class="btn btn-white btn-minuse" @click="changeAmount" type="button">-</button>
                                    </span>
                                    <input v-model="form.Amount" type="text" class="form-control no-padding add-color text-center height-25" maxlength="3">
                                    <span class="input-group-btn">
                                        <button class="btn btn-red btn-pluss" @click="changeAmount" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        
                        </div>	
                        
                        <div class="row mt-3">
                            <button class="btn btn-primary" @click="AddCart"><i class="fas fa-shopping-cart mr-2" ></i>Đặt vào giỏ hàng</button>
                        
                            <router-link :to="'/checkout/' + myProduct?.ID_Product + '/' + form.Amount " class="btn btn-success ml-3" value="Mua ngay" type="submit" >Mua ngay</router-link>
                            
                        </div>
                        <div class="row mt-3">
                            Tồn kho: {{ myProduct?.Amount_Product }}
                        </div>
                    </div>
                
                </div>
                    
            </div>
        </div>
        <div class="row ms-0 mt-5 w3-animate-bottom">
            <div class="row border w-100" data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="50" style="position: relative;">
                <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
                <div class="container-fluid">
                    <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#section1">Thông tin sản phẩm</a>
                    </li>
                    <!--   <li class="nav-item">
                        <a class="nav-link" href="#section2">Section 2</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#section3">Section 3</a>
                    </li> -->
                    </ul>
                </div>
                </nav>

                <div id="section1" class="container-fluid bg-light text-dark" style="padding:100px 20px;">
                <h1>Thông tin sản phẩm</h1>
                <div>
                    {{ myProduct?.Description }}
                </div>
                </div>

                <!-- <div id="section2" class="container-fluid bg-warning" style="padding:100px 20px;">
                <h1>Section 2</h1>
                <p>Try to scroll this section and look at the navigation bar while scrolling! Try to scroll this section and look at the navigation bar while scrolling!</p>
                <p>Try to scroll this section and look at the navigation bar while scrolling! Try to scroll this section and look at the navigation bar while scrolling!</p>
                </div>

                <div id="section3" class="container-fluid bg-secondary text-white" style="padding:100px 20px;">
                <h1>Section 3</h1>
                <p>Try to scroll this section and look at the navigation bar while scrolling! Try to scroll this section and look at the navigation bar while scrolling!</p>
                <p>Try to scroll this section and look at the navigation bar while scrolling! Try to scroll this section and look at the navigation bar while scrolling!</p>
                </div> -->
            </div>
            <div class="card flex justify-content-center">
                <Rating v-model="rating">
                    <template #cancelicon>
                        <img src="https://primefaces.org/cdn/primevue/images/rating/cancel.png" height="24" width="24" />
                    </template>
                    <template #onicon>
                        <img src="https://primefaces.org/cdn/primevue/images/rating/custom-onicon.png" height="24" width="24" />
                    </template>
                    <template #officon>
                        <img src="https://primefaces.org/cdn/primevue/images/rating/custom-officon.png" height="24" width="24" />
                    </template>
                </Rating>
            </div>
        </div>
        <div class="container bg-light rounded pt-2 mt-3">
            <div class="row">
                <h3 class="h3 w3-animate-opacity">Các sản phẩm có liên quan</h3>    
            </div>
            <div class="row w-100">
                <!-- <?php foreach ($data['Product_Suggestion'] as $key => $value) { 
                    $title = $value['Name_Product'];
                    if (strlen($value['Name_Product']) > 34) {
                    $title = mb_substr($value['Name_Product'], 0, 34) . "...";
                    }
                    ?>
                <div class="col-md-2 col-sm-6 bg-light pt-3 border-end border-2 rounded rounded-3 w3-animate-bottom-08 mt-3 mb-4">
                        <div class="product-grid2">
                            <div class="product-image2">
                                <a href="/Home/Info/<?=$value['ID_Product']?>">
                                    <img class="pic-1" src="<?= $value['Avatar']?>">
                                </a>
                                <ul class="social">
                                    <li><a href="#" data-tip="Quick View"><i class="fa fa-eye"></i></a></li>
                                    <li><a href="#" data-tip="Add to Cart"><i class="fa fa-shopping-cart"></i></a></li>
                                </ul>
                                <a class="add-to-cart" href="">Add to cart</a>
                            </div>
                            <div class="product-content">
                                <h3 class="title"><a href="#"><?=$title?></a></h3>
                                <span class="price"><?=number_format($value['Price'],0) . ' đ'?></span>
                            </div>
                        </div>
                    </div> 
                <?php }?> -->
                

            </div>
        </div>
    </div>
</template>