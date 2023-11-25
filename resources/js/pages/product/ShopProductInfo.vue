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
import { product,comment, SaleOff, Review_Rating_Response } from '../../interface';
import axios, { formToJSON } from 'axios';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import Galleria from 'primevue/Galleria';
import SelectButton from 'primevue/selectbutton';
import Image from 'primevue/image';
import Rating from 'primevue/rating';
import Breadcrumb from 'primevue/breadcrumb';
import Skeleton from 'primevue/skeleton';
import Badge from 'primevue/badge';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import ProgressSpinner from 'primevue/progressspinner';
const route = useRoute();
const router = useRouter();
const myProduct = ref<product>();
const myComment = ref<comment[]>();
const myProductRelative = ref<product[]>();
const activeIndex = ref(1);
const rating = ref(0);
const tonKho = ref(-1);
const isLogin = ref(false);
const sales = reactive<SaleOff[]>([]);
const loadingSales = ref(false);
const visibleEditComment = ref(false);
const commentWantEdit = ref<comment>();
const visibleButtonDeleteAndEditReply = ref(false);
interface MyImage {
    itemImageSrc: string;
    thumbnailImageSrc: string;
    alt: string;
}
const id_user = (document.getElementById('id_user_login') as HTMLInputElement).value;

const images = ref<MyImage[]>([]);
const ratings = ref<Review_Rating_Response>();
const home_breadcrumb = ref({
    icon: 'fas fa-home',
    to: '/',
});
const isSearching = ref(false);
const productsSimilar = ref<product[]>();
const pathImageChoosedToSearch = ref<string>();
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
const comment_content = ref('');
const Comment = (event:Event,reply_to?:number,replyContent?:string) => {
    event.preventDefault();
    let content = comment_content.value;
    if(replyContent)
        content = replyContent;
    axios.post('/api/comment',{ 
        content:content,
        ID_Product:myProduct.value?.ID_Product,
        reply_to:reply_to,
    }).then(res=>{
        if(res.status == 200)
        {
            LazyCodet.AlertSuccess(res.data.message);
            loadComment();
        }
        else
        {
            LazyCodet.AlertError(res.data.message);
        }
    })
}
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
const currentTabRating = ref(0);
const currentListRating = computed(()=>{
    if(currentTabRating.value == 0)
        return ratings.value?.all;
    else if(currentTabRating.value == 1)
        return ratings.value?._1;
    else if(currentTabRating.value == 2)
        return ratings.value?._2;
    else if(currentTabRating.value == 3)
        return ratings.value?._3;
    else if(currentTabRating.value == 4)
        return ratings.value?._4;
    else if(currentTabRating.value == 5)
        return ratings.value?._5;
});
let form = reactive<FormState>(JSON.parse(JSON.stringify(initForm)));
const checkingAmount = ref(false);
function DeleteComment(cmt: comment)
{
    LazyCodet.AlertProcessing({
        workerFunction() {
            return axios.post('/api/delete-comment',{idComment: cmt.id}).then(res=>{
                if(res.data.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    let index:any = -1;
                    let indexCmt:any = -1;
                    if(cmt.reply_to)
                    {
                        indexCmt =  myComment.value?.findIndex(c => c.id == cmt.reply_to);
                        index = myComment.value![indexCmt!].replies.findIndex(c => c.id == cmt.id);
                    }
                    else
                        index = myComment.value?.findIndex(c => c.id == cmt.id);

                    if(index !== -1)
                    {
                        if(cmt.reply_to)
                        {
                            myComment.value![indexCmt].replies.splice(index!,1);
                        }
                        else
                        {
                            myComment.value!.splice(index!,1);
                        }
                        
                    }
                }
                else{
                    LazyCodet.AlertError(res.data.message);
                }
            })
        },
    })
}

function CheckAmount()
{
    if((isNotHaveColor() || form.ID_Color) && (isNotHaveSize() || form.ID_Dimensions) && (isNotHaveMaterial() || form.ID_Material)) {
        let myform = {
            ID_Color:form.ID_Color,
            ID_Material:form.ID_Material,
            ID_D:form.ID_Dimensions,
            ID_Product: myProduct.value?.ID_Product
        }
        checkingAmount.value = true;
        axios.post('/api/product/amount',myform).then(res=>{
            console.log(res.data);
            checkingAmount.value = false;

            if(res.data.status == 200)
            {
                tonKho.value = res.data.value;
            }
            else
            {
                LazyCodet.AlertError(res.data.message);
            }
        });
    }
}
// Đoạn watch này không hoạt động trên MacOs (nó không trigger khi thay đổi giá trị)
// watch([form.ID_Color,form.ID_Dimensions, form.ID_Material], (values: [number|null, number|null, number|null]) => {
//     CheckAmount();
// });
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
function EditComment()
{
    visibleEditComment.value = false;
    LazyCodet.AlertProcessing({
        requireConfirm: false,
        workerFunction() {
            return axios.post("/api/edit-comment", {
                id: commentWantEdit.value!.id,
                content: commentWantEdit.value?.app_replyContent

            }
                ).then(res=>{
                    if(res.data.status == 200)
                    {
                        LazyCodet.AlertSuccess(res.data.message);
                        commentWantEdit.value!.content = commentWantEdit.value!.app_replyContent;
                    }
                    else
                    {
                        LazyCodet.AlertError(res.data.message);
                    }
                })
        },
    })
}
const loadComment = () => {
    axios.get('/api/comment/' + myProduct.value?.ID_Product).then(res=>{
        if(res.data.status == 200)
        {
            myComment.value = res.data.object;
        }
        else
        {
            LazyCodet.AlertError(res.data.message);
        }
    });
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
    console.log(myProduct.value?.detail_product_color);
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
const loadRatings = () =>
{
    axios.post(`/api/rating/${route.params.id}` ).then(res=>{
        ratings.value = res.data.object;
    });
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
        loadComment();

        //Nếu như mà trường hợp sản phẩm không có màu sắc chất liệu hay size nào hết sẽ kiểm tra thử xem có tồn tại sản phẩm tồn nào không
        if(isNotHaveSize() && isNotHaveColor() && isNotHaveMaterial())
        {
            CheckAmount();
        }
    });
}
async function SearchSimilar(avatar: string)
{
    try {
        pathImageChoosedToSearch.value! = avatar;
        const blob = await fetch(avatar).then((r) => r.blob());
        const reader = new FileReader();

        reader.readAsDataURL(blob);

        reader.onloadend = function () {
            const base64data = reader.result as string;

            // Chuyển đổi base64data thành ArrayBuffer
            const byteCharacters = atob(base64data.split(',')[1]);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const arrayBuffer = byteArray.buffer;

            // Tạo đối tượng File từ ArrayBuffer
            const file = new File([arrayBuffer], "avatar.jpg", { type: "image/jpeg" });

            const formData = new FormData();
            formData.append("file", file);

            isSearching.value = true;
            
            axios.post(`http://localhost:8001/image`, formData).then(res => {
                console.log(res.data.folders);

                axios.post('/api/getImagesSpecial', { names: res.data.folders.toString() }).then(res => {
                    productsSimilar.value = res.data;
                    isSearching.value = false;
                });
            });
        };
    } catch (error) {
        console.error('Error fetching or reading the image:', error);
    }
}
onMounted(()=>{
    
    loadProduct();
    loadRatings();
    loadingSales.value = true;
    axios.post('/api/showSale/' + route.params.id).then(res=>{
        Object.assign(sales, (res.data as any).sales);
        loadingSales.value = false;
    })

    axios.post('/api/shoppingcart/get_carts_not_checkout').then(res => {
        let array = res.data;
        if(array.length > 0 )
        {
            setCountCard(array.length);
        }
    });
    axios.post('/api/checklogin').then(res => {
        isLogin.value = res.data.isLogin;
    });
    
});
</script>
<template>
    <div class="p-5 mt-5 bg-light" style="border-radius: 25px;">
        <div class="row mb-5">
            <Breadcrumb v-if="items_Breadcrumb.length >0" :home="home_breadcrumb" :model="items_Breadcrumb" />
            <Skeleton v-else height="2rem" class="mb-2"></Skeleton>

        </div>
        <div class="row w-100">
            <div class="col-lg-5">
                <div class="p-3">
                        <Galleria v-if="myProduct" :value="images" :responsiveOptions="responsiveOptions" :numVisible="visibleCount" containerStyle="max-width: 640px">
                            <template #item="slotProps">
                                <Image :src="slotProps.item.itemImageSrc" height="400"  :alt="slotProps.item.alt" :id="'product-avt'"  preview />
                            </template>
                            <template #thumbnail="slotProps">
                                <img :src="slotProps.item.thumbnailImageSrc" width="100" height="100" :alt="slotProps.item.alt" />
                            </template>
                        </Galleria>
                        <div v-else>
                            <Skeleton height="20rem" class="mb-2"></Skeleton>
                            <Skeleton height="10rem" class="mb-2"></Skeleton>

                        </div>

                    
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
                    <h1 v-if="myProduct">{{ myProduct?.Name_Product }}</h1>
                    <Skeleton v-else height="4rem" class="mb-2"></Skeleton>
                    <div v-if="myProduct && myProduct.detail_sale_of_product.length>0">
                        <del class="text-primary h3"><strong>{{ LazyConvert.ToMoney(myProduct?.Price) }}</strong></del>
                        <p class="text-danger h3"><strong>{{ LazyConvert.ToMoney(myProduct?.Price_SaleOff) }}</strong></p>
                        
                    </div>
                    <p v-else-if="myProduct" class="text-danger h3"><strong>{{ LazyConvert.ToMoney(myProduct?.Price) }}</strong></p>
                    <Skeleton v-else height="2rem" class="mb-2"></Skeleton>

                    <hr>
                    <div v-if="myProduct">
                        <div>
                            <span><b>Màu sắc:</b></span>
                            <span v-if="isNotHaveColor()">
                                {{ ' Không xác định' }}
                            </span>
                            <SelectButton  v-else @change="CheckAmount()" v-model="form.ID_Color" optionValue="ID_Color" class="mt-3" :options="myProduct?.detail_product_color" optionLabel="color.Name_Color"  />
                        </div>
                        
                        <div class="mt-4">
                            <span><b>Chất liệu:</b></span>
                            <span v-if="isNotHaveMaterial()">
                                {{ ' Không xác định' }}
                            </span>
                            <SelectButton  v-else @change="CheckAmount()" v-model="form.ID_Material" optionValue="ID_Material" class="mt-3" :options="myProduct?.detail_product_material" optionLabel="material.Name_Material"  />
                        </div>
                        <div class="mt-4">
                            <span><b>Kích thước:</b></span>
                            <span v-if="isNotHaveSize()">
                                {{ ' Không xác định' }}
                            </span>
                            <SelectButton  v-else @change="CheckAmount()" v-model="form.ID_Dimensions" optionValue="ID_D" class="mt-3" :options="myProduct?.dimensions" optionLabel="Name_D"  />
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
                        
                            <button @click="BuyNow" class="btn btn-success ml-3" :disabled="tonKho <= 0" value="Mua ngay" type="submit" >Mua ngay</button>
                            
                        </div>
                        <div class="row mt-3">
                            <b class="mr-2">Tồn kho:</b> <Badge v-if="checkingAmount" value="Checking..."></Badge>
                                     <Badge v-else-if="tonKho == -1" value="Chưa xác định"></Badge>
                                     <Badge v-else :value="tonKho"></Badge>
                        </div>
                    </div>
                    <Skeleton v-else height="20rem" class="mb-2"></Skeleton>

                </div>
                    
            </div>
        </div>
        <div class="row">
            <h1>Thông tin khuyến mãi</h1>
            
            <DataTable :value="sales" tableStyle="min-width: 50rem" class="w-100 card" :loading="loadingSales" stripedRows >
                <template v-if="!loadingSales" #empty> Sản phẩm này chưa có khuyến mãi nào. </template>
                <Column field="sale_off.Name_SO" header="Tên khuyến mãi"></Column>
                <Column field="sale_off.Discount_Percent_SO" header="Giảm giá (%)"></Column>
                <Column field="sale_off.Start_Date_SO" header="Ngày bắt đầu"></Column>
                <Column field="sale_off.End_Date_SO" header="Ngày kết thúc"></Column>
                <Column header="Tình trạng">
                
                <template #body="slotProps">
                    <span v-if="slotProps.data.Apply == 1"><Badge value="Đang áp dụng"></Badge></span>
                    <span v-else-if="slotProps.data.Apply == 2"><Badge severity="danger" value="Đã áp dụng"></Badge></span>
                    <span v-else>Chưa áp dụng</span>
                </template>
                </Column>
               
            </DataTable>
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
                <div v-if="myProduct">
                    {{ myProduct?.Description }}
                </div>
                <Skeleton v-else height="20rem" class="mb-2"></Skeleton>

                </div>

              
            </div>
            
            <div class="d-flex flex-column w-100">
                <h3><b>Đánh giá sản phẩm</b></h3>
                <hr>
                <div class="d-flex w-100">
                    <div class="d-flex flex-column card" style="width: 100px;">
                        <button @click="currentTabRating = 0" class="btn btn-light" :class="{active: currentTabRating == 0}">Tất cả</button>
                        <button @click="currentTabRating = 5" class="btn btn-light" :class="{active: currentTabRating == 5}">5 <img src="/custom-onicon.png" width="20" height="20" /> ({{ ratings?._5.length }})</button>
                        <button @click="currentTabRating = 4" class="btn btn-light" :class="{active: currentTabRating == 4}">4 <img src="/custom-onicon.png" width="20" height="20" /> ({{ ratings?._4.length }})</button>
                        <button @click="currentTabRating = 3" class="btn btn-light" :class="{active: currentTabRating == 3}">3 <img src="/custom-onicon.png" width="20" height="20" /> ({{ ratings?._3.length }})</button>
                        <button @click="currentTabRating = 2" class="btn btn-light" :class="{active: currentTabRating == 2}">2 <img src="/custom-onicon.png" width="20" height="20" /> ({{ ratings?._2.length }})</button>
                        <button @click="currentTabRating = 1" class="btn btn-light" :class="{active: currentTabRating == 1}">1 <img src="/custom-onicon.png" width="20" height="20" /> ({{ ratings?._1.length }})</button>
                    </div>
                    <div class="p-2 card w-100 h-100">
                        <div v-if="ratings && ratings.total == 0" class="text-center d-flex justify-content-center align-items-center h-100">
                            <h2>Sản phẩm chưa có đánh giá nào !</h2>
                        </div>
                        <div v-else-if="ratings">
                            <div v-if="currentListRating?.length == 0" class="text-center d-flex justify-content-center align-items-center h-100">
                                <h2>Không có đánh giá nào ở mức độ hài lòng là {{ currentTabRating }}</h2>
                            </div>
                            <div v-for="rat in currentListRating" class="mb-5">
                            
                                <div class="media-body">
                                    <img width="50" height="50" src="/avatar.png" class="mr-3 rounded-circle" alt="...">
                                    <h6 class="mt-0" style="display:contents;"><strong class="ms-2">{{ rat.user.Email.split('@')[0] }}</strong>  </h6>
                                    <small>{{' ' + new Date(rat.created_at).toLocaleString()  }}</small>
                                    <Rating class="m-3" v-model="rat.rate.Value_Rate" :cancel="false">
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
                               

                                </div>
                                <div class="content-comment p-3">
                                {{ rat.Content_RR }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
          
        </div>
        <div class="row">
            <div class="card mb-3 w-100">
                <input type="text" id="offset_comment" value="0" hidden>
                <div class="card-header h4">Comments</div>
                <p v-if="isLogin == false" class="p-3 h6">Vui lòng đăng nhập để tham gia comment !</p>
                <div v-else class="card-body">
                    <form @submit="Comment($event)">
                        <div class="form-group">
                            <textarea v-model="comment_content" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Comment</button>
                    </form>
                    <hr>
                    <h5>Comments {{ myComment?.length }}:</h5>
                    <div id="comment_box">
                        <div v-for="cmt in myComment" data-reply-comment-total="<?=$value['reply_comment_total']?>" class="mb-5">
                            
                            <div class="media-body">
                                <img width="50" height="50" src="/avatar.png" class="mr-3 rounded-circle" alt="...">
                                <h6 class="mt-0" style="display:contents;"><strong class="ms-2">{{ cmt.user.Email.split('@')[0] }}</strong>  </h6>
                                <small>{{' ' + new Date(cmt.created_at).toLocaleString()  }}</small>
                                <!-- --Ellipsis -->
                                <div v-if="id_user == cmt.user.ID_User.toString()" class="d-flex justify-content-end" >
                                    <button type="button"  class="btn-ellipsis btn bg-white p-0 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="btn-ellipsis fas fa-ellipsis-h d-flex flex-row-reverse"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                    
                                        <button @click="visibleEditComment = true; commentWantEdit = cmt; commentWantEdit.app_replyContent = cmt.content" class="dropdown-item" data-bs-target="#updateModal" type="button" data-bs-toggle="modal">Edit</button>
                                        <button @click="DeleteComment(cmt)" class="dropdown-item" >Delete</button>
                    
                                    </div>
                                </div>
                                <!--// --Ellipsis -->

                            </div>
                            <div class="content-comment p-3">
                            {{ cmt.content }}
                            </div>
                            
                        <div class="box_reply card ms-3 <?=$hide_box_reply?>">
                            <div id="heading_<?= $value['ID_C'] ?>">
                            <h6 class="mb-0">
                                <button class="btn-read-reply btn btn-link" type="button" data-toggle="collapse" :data-target="'#collapse_' + cmt.id" aria-expanded="false" :aria-controls="'collapse_' + cmt.id">
                                Reply comment ({{ cmt.replies.length }})
                                </button>
                            </h6>
                            </div>

                            <div :id="'collapse_' + cmt.id" class="collapse" :aria-labelledby="'heading_' + cmt.id">
                            <div class="card-body">
                                <div class="comment_answer_box">
                                    <div v-for="rep in cmt.replies" >
                                        
                                        <div class="media-body">
                                            <img width="50" height="50" src="/avatar.png" class="mr-3 rounded-circle" alt="...">
                                            <h6 class="mt-0" style="display:contents;"><strong class="ms-2">{{ rep.user.Email.split('@')[0] }}</strong>  </h6>
                                            <small>{{' ' + new Date(rep.created_at).toLocaleString()  }}</small>
                                            <!-- --Ellipsis -->
                                            <div v-if="id_user == cmt.user.ID_User.toString()" class="d-flex justify-content-end">
                                                <div v-if="visibleButtonDeleteAndEditReply">
                                                    <button @click="visibleEditComment = true; commentWantEdit = rep; commentWantEdit.app_replyContent = rep.content" class="dropdown-item" data-bs-target="#updateModal" type="button" data-bs-toggle="modal">Edit</button>
                                                    <button @click="DeleteComment(rep)" class="dropdown-item" >Delete</button>
                    
                                                </div>
                                                <button @click="visibleButtonDeleteAndEditReply = !visibleButtonDeleteAndEditReply" type="button" class="btn-ellipsis btn bg-white p-0 dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="btn-ellipsis fas fa-ellipsis-h d-flex flex-row-reverse"></i>
                                                </button>
                                                
                                            </div>
                                            <!--// --Ellipsis -->

                                        </div>
                                        <div class="content-comment-reply p-3">
                                            {{ rep.content }}
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="spinner-border m-5" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div> -->
                                
                            </div>
                            </div>
                        </div>
                            <button type="button" @click="cmt.app_replyFormVisible = !cmt.app_replyFormVisible" class="btn btn-secondary btn-reply">Reply</button>
                            <div class="reply-form mt-3" v-show="cmt.app_replyFormVisible">
                                <form @submit="Comment($event,cmt.id,cmt.app_replyContent)">
                                    <div class="form-group">
                                    <textarea v-model="cmt.app_replyContent" class="form-control" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Comment</button>
                                </form>
                            </div>

                        </div>
                    </div>
                    <!-- <div class="spinner-border m-5" role="status">
                    <span class="sr-only">Loading...</span>
                    </div> -->
                    
                </div>
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
        <div class="bg-light rounded pt-2 mt-3">
            <div class="row">
                <h3 class="h3 w3-animate-opacity">Nhấn vào hình ảnh để tìm kiếm hình ảnh tương tự</h3>    
            </div>
            <div class="row w-100">
                <div v-for="detail in myProductRelative?.slice(0,6)" class="col-md-2 col-sm-6 bg-light pt-3 border-end border-2 rounded rounded-3 w3-animate-bottom-08 mt-3 mb-4">
                    <div class="product-grid2">
                        <div class="product-image2">
                           
                            <img @click="SearchSimilar(detail.Avatar)" class="pic-1 border border-warning" :src="detail.Avatar">
                            <img v-if="pathImageChoosedToSearch == detail.Avatar" class="position-absolute" style="top:0; right: 0;" src="/tick.png"/>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div v-if="pathImageChoosedToSearch" class="bg-light rounded pt-2 mt-3">
            <div class="row">
                <h5 class="w3-animate-opacity"><b>Kết quả ảnh tương tự đã chọn:</b></h5>    
            </div>
            <div class="row w-100">
                <ProgressSpinner v-if="isSearching" />
                <div v-else>
                    <!-- <div><img :src="pathImageChoosedToSearch" width="150" height="150" /> Hình ảnh bạn chọn</div>  -->
                    <div class="row">

                        <div v-for="detail in productsSimilar" class="col-md-2 col-sm-6 bg-light pt-3 border-end border-2 rounded rounded-3 w3-animate-bottom-08 mt-3 mb-4">
                            
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
        </div>
    </div>
    
    <Dialog v-model:visible="visibleEditComment" header="Sửa bình luận"
                :pt="{
                    mask: {
                        style: 'backdrop-filter: blur(2px)'
                    }
                }"
    >

            <div class="form-group">
                <textarea v-model="commentWantEdit!.app_replyContent" class="form-control" rows="3" cols="70"></textarea>
            </div>
            <button @click="EditComment()" class="btn btn-primary">Lưu thay đổi</button>
    </Dialog>
</template>