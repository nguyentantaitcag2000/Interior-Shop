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
import {onMounted, ref,reactive,computed,watch,watchEffect} from 'vue';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import { product } from '../../interface';
import axios, { formToJSON } from 'axios';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import Galleria from 'primevue/Galleria';
import SelectButton from 'primevue/selectbutton';
import Image from 'primevue/image';
import Rating from 'primevue/rating';
import Breadcrumb from 'primevue/breadcrumb';

const route = useRoute();
const router = useRouter();
const myProduct = ref<product>();
const myProductRelative = ref<product[]>();
const activeIndex = ref(1);
const rating = ref(0);
interface MyImage {
    itemImageSrc: string;
    thumbnailImageSrc: string;
    alt: string;
}

const images = ref<MyImage[]>([]);
const home_breadcrumb = ref({
    icon: 'fas fa-home',
    to: '/',
});
interface breadcrumb{
    label:string,
    to:string,
}
const items_Breadcrumb = reactive<breadcrumb[]>([]);
interface FormState{
    ID_Product: number,
    ID_Color: number|null,
    ID_Material: number|null,
    ID_Dimensions: number|null,
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
watch(()=> route.params, ()=>{
    images.value = [];
    window.scrollTo(0,0);
    form = reactive<FormState>(JSON.parse(JSON.stringify(initForm)));
    loadProduct();
});
const visibleCount = computed(() => {
    return images.value.length > 3 ? 3 : images.value.length;
});
const initForm = {
    ID_Product: -1,
    Amount: 1,
    ID_Color: null,
    ID_Material: null,
    ID_Dimensions: null
}
let form = reactive<FormState>(JSON.parse(JSON.stringify(initForm)))
const changeAmount = (event:Event) => {
    let button = event.target as HTMLElement;

    if(button.className.includes('btn-minuse'))
        form.Amount = form.Amount > 1 ? form.Amount -1 : form.Amount;
    else
        form.Amount++;
}
const checkChooseColor = ()=>{
    if(isNotHaveColor() == false && form.ID_Color == null)
    {
        LazyCodet.AlertError("Vui lòng chọn màu sắc của sản phẩm !");
        throw '';
    }
}
const checkChooseMaterial = ()=>{
    if(isNotHaveMaterial() == false && form.ID_Material == null)
    {
        LazyCodet.AlertError("Vui lòng chọn chất liệu của sản phẩm !");
        throw '';
    }
}
const checkChooseSize = ()=>{
    if(isNotHaveSize() == false && form.ID_Dimensions == null)
    {
        LazyCodet.AlertError("Vui lòng chọn kích thước của sản phẩm !");
        throw '';
    }
}
const AddCart = ()=>{
    checkChooseColor();
    checkChooseMaterial();
    checkChooseSize();

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
const BuyNow = () => {
    checkChooseColor();
    checkChooseMaterial();
    checkChooseSize();
    router.push('/checkout/' + myProduct.value?.ID_Product + '/' + form.Amount + '/' +  form.ID_Color + '/' +  form.ID_Material + '/' +  form.ID_Dimensions );
}
const isNotHaveColor = ()=>{
    if(myProduct.value?.detail_product_color.length == 0)
        return true;
    return myProduct.value?.detail_product_color.length == 1 && myProduct.value?.detail_product_color[0].ID_Color == 0;
}
const isNotHaveMaterial = ()=>{
    if(myProduct.value?.detail_product_material.length == 0)
        return true;
    return myProduct.value?.detail_product_material.length == 1 && myProduct.value?.detail_product_material[0].ID_Material == 0;
}
const isNotHaveSize = ()=>{
    if(myProduct.value?.dimensions.length == 0)
        return true;
    return false;
}
const loadProduct = () => {
    form.ID_Product = Number(route.params.id);
    axios.get('/api/product/' + route.params.id).then(res => {
        myProduct.value = res.data;
        let category = myProduct.value?.category.Name_Category == undefined ? '' : myProduct.value?.category.Name_Category;
        items_Breadcrumb.splice(0,items_Breadcrumb.length);
        items_Breadcrumb.push({
            label:category,
            to:'/?category=' + category
        });
        items_Breadcrumb.push({
            label:myProduct.value?.Name_Product ? myProduct.value?.Name_Product  : '',
            to:'#'
        });
        console.log(items_Breadcrumb);
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
        axios.get(`/api/products/category/${myProduct.value?.category.Name_Category}` ).then(res=>{
            myProductRelative.value = res.data;
        });
    });
}
onMounted(()=>{
    
    loadProduct();
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
    <div class="p-5 mt-5 bg-light" style="border-radius: 25px;">
        <div class="row mb-5">
            <Breadcrumb :home="home_breadcrumb" :model="items_Breadcrumb" />
        </div>
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
                    <hr>
                    <div>
                        <div>
                            <span><b>Màu sắc:</b></span>
                            <span v-if="isNotHaveColor()">
                                {{ ' Không xác định' }}
                            </span>
                            <SelectButton v-else v-model="form.ID_Color" optionValue="ID_Color" class="mt-3" :options="myProduct?.detail_product_color" optionLabel="color.Name_Color"  />
                        </div>
                        
                        <div class="mt-4">
                            <span><b>Chất liệu:</b></span>
                            <span v-if="isNotHaveMaterial()">
                                {{ ' Không xác định' }}
                            </span>
                            <SelectButton v-else v-model="form.ID_Material" optionValue="ID_Material" class="mt-3" :options="myProduct?.detail_product_material" optionLabel="material.Name_Material"  />
                        </div>
                        <div class="mt-4">
                            <span><b>Kích thước:</b></span>
                            <span v-if="isNotHaveSize()">
                                {{ ' Không xác định' }}
                            </span>
                            <SelectButton v-else v-model="form.ID_Dimensions" optionValue="ID_D" class="mt-3" :options="myProduct?.dimensions" optionLabel="Name_D"  />
                        </div>

                        <div class="row g-3 align-items-center mt-4">
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
                        
                            <button @click="BuyNow" class="btn btn-success ml-3" value="Mua ngay" type="submit" >Mua ngay</button>
                            
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
        <div class="bg-light rounded pt-2 mt-3">
            <div class="row">
                <h3 class="h3 w3-animate-opacity">Các sản phẩm có liên quan</h3>    
            </div>
            <div class="row w-100">
                <!-- <div v-for="pr in products" class="col-md-2 col-sm-6 bg-light pt-3 border-end border-2 rounded rounded-3 w3-animate-bottom-08 mt-3 mb-4">
                    <div class="product-grid2">
                        <div class="product-image2">
                            <router-link :to="'/product/' + pr.ID_Product" >
                                <img class="pic-1" :src="pr.Avatar">
                            </router-link>

                        </div>
                        <div class="product-content">
                            <h3 class="title"><a href="#">{{ pr.Name_Product }}</a></h3>
                            <span class="price">{{ LazyConvert.ToMoney(pr.Price) }}</span>
                        </div>
                    </div>
                </div> -->

                <div v-for="detail in myProductRelative" class="col-md-2 col-sm-6 bg-light pt-3 border-end border-2 rounded rounded-3 w3-animate-bottom-08 mt-3 mb-4">
                    <div class="product-grid2">
                        <div class="product-image2">
                            <router-link :to="'/product/' + detail.ID_Product">
                                <img class="pic-1" :src="detail.Avatar">
                            </router-link>
                        </div>
                        <div class="product-content">
                            <h3 class="title"><router-link :to="'/product/' + detail.ID_Product">{{ detail.Name_Product }}</router-link></h3>
                            <span class="price">{{ LazyConvert.ToMoney(detail.Price) }}</span>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</template>