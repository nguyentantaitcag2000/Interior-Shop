<script setup lang="ts">
import {onMounted,ref,watch} from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
import { LazyConvert } from '../lazycodet/lazycodet';
import { category, product } from '../interface';
import { setCountCard } from '../main';
import ProgressSpinner from 'primevue/progressspinner';
let myIndex = 0;
const route = useRoute();
const router = useRouter();
const categories = ref<category[]>();
const products = ref<product[]>();
interface Search{
    by:string,
    keyword:string
}
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
    
    <div class="container bg-light rounded pt-2 mt-3 change-background" data-background="#f6d9d9">
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
                        <h3 class="title"><a href="#">{{ pr.Name_Product }}</a></h3>
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
