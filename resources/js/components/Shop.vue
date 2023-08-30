<script setup lang="ts">
import {onMounted,ref} from 'vue';
import axios from 'axios';
import { LazyConvert } from '../lazycodet/lazycodet';
import { category, product } from '../interface';
let myIndex = 0;

const categories = ref<category[]>();
const products = ref<product[]>();
const productsInCard = ref();
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
onMounted(()=>{
    carousel();
    axios.get('/api/categories').then(res=>{
        categories.value = res.data;
    });
    axios.get('/api/products').then(res=>{
        products.value = res.data;
    });
    axios.get('/api/shoppingcart').then(res=>{
        productsInCard.value = res.data;
    });
});

</script>


<template>
    <div class="w3-content w3-display-container w3-animate-right-08">
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
            <a href="/Home/Search/Category/<?= $value['Name_Category']?>">
                <img class="mx-auto d-block" :src="ca.Icon">
                <p class="text-center">{{ ca.Name_Category }}</p>
            </a>
        </div>
      </div>
    </div>

    <div class="container bg-light rounded pt-2 mt-3 change-background" data-background="#f6d9d9">
        <div class="row">
            <h3 class="h3 w3-animate-opacity">Danh sách sản phẩm</h3>
        </div>
        <div class="row w-100">
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
