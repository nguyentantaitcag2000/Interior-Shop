<script setup lang="ts">
import {onMounted,ref,watch} from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
import { LazyCodet, LazyConvert } from '../lazycodet/lazycodet';
import { category, product } from '../interface';
import { setCountCard } from '../main';
import ProgressSpinner from 'primevue/progressspinner';
import Toast from 'primevue/toast';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import Carousel from 'primevue/carousel';
let myIndex = 0;
const route = useRoute();
const router = useRouter();
const categories = ref<category[]>();
const products = ref<product[]>();
const toast = useToast();
interface Search{
    by:string,
    keyword:string
}
interface FilterState{
    name:string,
    value:number
}
enum FILTER {
    High2Low,
    Low2High
}

const filter_by = ref();
const options_filter = ref<FilterState[]>([
    {
        name: 'Giá thấp đến cao',
        value: FILTER.Low2High
    },
    {
        name: 'Giá cao đến thấp',
        value: FILTER.High2Low
    }
]);
watch(()=> filter_by.value, (newVal:FilterState) => {
    LazyCodet.AlertProcessing({
        requireConfirm:false,
        workerFunction() {
            toast.add({ severity: 'success', summary: 'Olala', detail: 'Đã lọc ' + newVal.name,life:2000 });
            return axios.get('/api/filter/' + newVal.value).then(res=>{
                products.value = res.data.object;
            });
        },
    })


});
// const getSeverity = (status:string) => {
//     switch (status) {
//         case 'INSTOCK':
//             return 'success';

//         case 'LOWSTOCK':
//             return 'warning';

//         case 'OUTOFSTOCK':
//             return 'danger';

//         default:
//             return null;
//     }
// };
const responsiveOptions = ref([
    {
        breakpoint: '1199px',
        numVisible: 3,
        numScroll: 3
    },
    {
        breakpoint: '991px',
        numVisible: 2,
        numScroll: 2
    },
    {
        breakpoint: '767px',
        numVisible: 1,
        numScroll: 1
    }
]);
function carousel() {
  var i;
  var timeOut = 4000;
  var x = document.getElementsByClassName("mySlides") as HTMLCollectionOf<HTMLElement>;;
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  myIndex++;
  if (myIndex > x.length) {myIndex = 1}
  x[myIndex-1].style.display = "block";
  var dataTimeout = x[myIndex-1].getAttribute('data-timeout');
  if(dataTimeout!=null)
    timeOut = parseInt(dataTimeout);
  setTimeout(carousel, timeOut); // Change image every 2 seconds

}


let search:Search = {
            by:'_',
            keyword: '_'
        };
const isSearching  = ref(true);

const loadProduct = () => {
    isSearching.value = true;
    if(typeof route.query.category != 'undefined')
        search = {
            by:'category',
            keyword: route.query.category as string
        };
    else if(typeof route.query.search != 'undefined')
    {
        search = {
            by:'all',
            keyword: route.query.search as string
        };
    }
    else
        search = {
            by:'_',
            keyword: '_'
        };
    // console.log(`/api/products/${search.by}/${search.keyword}`);
    if(search.keyword.trim() == '')
    {
        search.keyword = '_';
        search.by = '_';
    }
    axios.get(`/api/products/${search.by}/${search.keyword}` ).then(res=>{
        products.value = res.data;
        isSearching.value = false;
    });
}

watch(()=> route.query, (oldVal,newVal)=>{
    loadProduct();
});
onMounted(()=>{
    carousel();
    let searchInput = document.getElementById('search') as HTMLInputElement;
    searchInput.addEventListener('keyup',function(event:KeyboardEvent){
        if(event.keyCode == 13)
            router.push({ path: '/', query: { search: this.value } });
    });
    document.getElementById('btn_search')?.addEventListener('click',()=>{
        router.push({ path: '/', query: { search: searchInput.value } });
    });
    axios.get('/api/categories').then(res=>{
        categories.value = res.data;
    });
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
    <div v-if="search.by!='all'" class="w3-content w3-display-container w3-animate-right-08">
      <img class="mySlides" src="../../../public/images/1.png" style="width:100%">
      <img class="mySlides" src="../../../public/images/2.png" style="width:100%;display:none;">
      <img class="mySlides" data-timeout="8000" src="../../../public/images/3.png" style="width:100%;display:none;">
      <img class="mySlides" data-timeout="8000" src="../../../public/images/4.png" style="width:100%;display:none;">
      <img class="mySlides" data-timeout="10000" src="../../../public/images/5.png" style="width:100%;display:none;">
      <!-- <button class="w3-button w3-black w3-display-left" onclick="plusDivs(-1)">&#10094;</button> -->
      <!-- <button class="w3-button w3-black w3-display-right" onclick="plusDivs(1)">&#10095;</button> -->
    </div>

    <div class="menu-product container bg-light rounded pt-2 mt-3 change-background" data-background="#9adbf6" style="justify-content: center;">
        <div class="row mt-4 mb-4" >
            <div v-for="ca in categories" class="col hover w3-animate-left-08">
                <router-link :to="'/?category=' + ca.Name_Category">
                    <img class="mx-auto d-block" :src="ca.Icon">
                    <p class="text-center">{{ ca.Name_Category }}</p>
                </router-link>
            </div>
        </div>
    </div>
    <hr>
    <div class="container bg-light rounded pt-2 mt-3 change-background" data-background="#f6d9d9">

        <div v-if="search.by=='_'" class="row mb-3">
                <h3 class="h3 w3-animate-opacity">Sản phẩm bán chạy233234 <img width="50" src="/new.gif" /></h3>
        </div>
        <div v-if="search.by=='_'" class="row mb-3">
            <div class="card">

                <Carousel   :value="products?.slice(0,28)" :numVisible="3" :numScroll="1" :responsiveOptions="responsiveOptions" circular :autoplayInterval="3000">
                    <template #item="slotProps">
                        <div style="height: 350px;" class="border-1 surface-border border-round m-2 text-center py-5 px-3">
                            <div class="mb-3">
                                <router-link :to="'/product/' + slotProps.data.ID_Product" >
                                    <img style="width: 200px;height: 220px" :src="slotProps.data.Avatar" :alt="slotProps.data.Avatar" class="w-6 shadow-2">
                                </router-link>
                            </div>
                            <div>
                                <h4 class="mb-1"><router-link :to="'/product/' + slotProps.data.ID_Product">{{  slotProps.data.Name_Product  }}</router-link></h4>
                                <h6 class="mt-0 mb-3">${{ LazyConvert.ToMoney( slotProps.data.Price) }}</h6>

                            </div>
                        </div>
                    </template>
                </Carousel>
            </div>
        </div>
        <div class="row mb-3 d-flex justify-content-end">
            <Toast />
            <Button icon="fas fa-filter"></Button>
            <Dropdown v-model="filter_by" :options="options_filter" optionLabel="name"  placeholder="Sắp xếp theo" ></Dropdown>
        </div>

        <div class="row">
            <h3 v-if=" search.by == 'category' " class="h3 w3-animate-opacity">Sản phẩm trong danh mục <b>"{{ search.keyword }}"</b></h3>
            <h3 v-else-if=" search.by == 'all' " class="h3 w3-animate-opacity">Kết quả tìm kiếm cho <b>"{{ search.keyword }}"</b></h3>
            <h3 v-else class="h3 w3-animate-opacity">Danh sách sản phẩm</h3>
        </div>
        <ProgressSpinner v-if="isSearching" />
        <div v-if="products?.length==0 && !isSearching" class="py-5">
            <h4>Không có sản phẩm nào !</h4>
        </div>
        <div v-else class="row w-100">


            <div v-for="pr in products" class="col-md-2 col-sm-6 bg-light pt-3 border-end border-2 rounded rounded-3 w3-animate-bottom-08 mt-3 mb-4">
                <div class="product-grid2">
                    <div class="product-image2">
                        <router-link :to="'/product/' + pr.ID_Product" >
                            <img class="pic-1" :src="pr.Avatar">
                        </router-link>

                    </div>
                    <div class="product-content">
                        <h3 class="title"><router-link :to="'/product/' + pr.ID_Product">{{ pr.Name_Product }}</router-link></h3>
                        <span class="price">{{ LazyConvert.ToMoney(pr.Price) }}</span>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- <?php if(isset($data['Product_Suggestion'] )){ ?>
    <div class="container bg-light rounded pt-2 mt-3">
        <div class="row">
            <h3 class="h3 w3-animate-opacity">Những sản phẩm liên quan khác</h3>
        </div>
        <div class="row w-100">
            <?php foreach ($data['Product_Suggestion'] as $key => $value) {
                $title = $value['Name_Product'];
                if (strlen($value['Name_Product']) > 34) {
                  $title = mb_substr($value['Name_Product'], 0, 34) . "...";
                }
                ?>
               <div class="col-md-2 col-sm-6 bg-light pt-3 border-end border-2 rounded rounded-3 w3-animate-bottom-08 mt-3 mb-4">
                    <div class="product-grid2">
                        <div class="product-image2">
                            <a href="/Home/Info/<?=$value['ID_Product']?>" target="_blank">
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
            <?php }?>


        </div>
    </div>
    <?php }?> -->

</template>

../interface2../interface2
