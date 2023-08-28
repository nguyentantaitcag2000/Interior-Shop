<script setup>
import axios from 'axios';
import {ref, reactive, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from "primevue/inputtext";

const products = ref([]);
const getUsers = () => {
    axios.get('/api/products').then((res)=>{
        products.value = res.data;
        
    });
}
// Khai báo fillters cho chức năng tìm kiếm ở datatable
const filters = reactive({
    global: {
        value: ''
    }
});
const formatCash = (amount) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
};
const updateProduct = (productData) => {
    console.log(productData);
}
const deleteProduct = (ID_Product) => {
    alert(ID_Product);
}
onMounted(()=>{
    getUsers();
});

</script>
<!-- 
<script>
import axios from 'axios';
import $ from 'jquery';
import DataTable from "datatables.net";

export default {
  data() {
    return {
      products: []
    };
  },

  methods: {
    getUsers() {
      axios.get('/api/products').then((res) => {
        this.products = res.data;
      });
    },
    formatCash(amount) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    },
    setupDataTable() {
      $('#table').DataTable({
        "columnDefs": [
          { "orderDataType": "data-sort", "targets": [1] }
        ]
      });
    }
  },
  mounted() {
    this.getUsers();
    const self = this;
    this.$nextTick(() => {
        
      // Sử dụng nextTick để đảm bảo DOM đã được cập nhật hoàn toàn giông document on ready của jquery
      setTimeout(function(){
        self.setupDataTable();  
      },1000);
      
    });
  }
}
</script> -->

<template>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" >
    <!-- <button type="button" data-bs-toggle="modal" data-bs-target="#insertModal" data-bs-whatever="@mdo" class="btn btn-success m-3">New product</button> -->
    <button type="button" data-toggle="modal" data-target="#insertModal" data-whatever="@mdo" class="btn btn-success m-3">New product</button>
    <button type="button" data-bs-toggle="modal" data-bs-target="#uploadModal" data-bs-whatever="@mdo" class="btn btn-success m-3">Upload file .csv</button>
    <DataTable v-model:filters="filters" :value="products" dataKey="ID_Product" tableStyle="min-width: 50rem" showGridlines stripedRows
                paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                :globalFilterFields="['Name_Product']" filterDisplay="row" :loading="loading" 
                >
        <template #header>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span class="text-xl text-900 font-bold">Danh sách các sản phẩm:</span>
                <Button icon="fas fa-sync" rounded raised />
            </div>
            <div class="d-flex justify-content-end">
                <span class="p-input-icon-left">
                    <i class="fas fa-holly-berry" />
                    <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                </span>
            </div>
        </template>
        <template #empty> No customers found. </template>
        <template #loading> Loading customers data. Please wait. </template>
        <Column field="ID_Product" sortable  header="Code"></Column>
        <Column header="Ảnh sản phẩm">
            <template #body="slotProps">
                <img style="width:50px;" :src="`${slotProps.data.Avatar.replace('/public','')}`" :alt="slotProps.data.Avatar" class="w-6rem shadow-2 border-round" />
            </template>
        </Column>
        <Column field="Name_Product" header="Tên sản phẩm"></Column>
        <Column field="Name_Color" header="Màu sắc"></Column>
        <Column field="Name_Material" header="Chất liệu"></Column>
        <Column header="Mô tả">
            <template #body="splotProps">
                {{ splotProps.data.Description.substring(0,65) }}
            </template>
        </Column>
        <Column header="Giá">
            <template #body="splotProps">
                {{ formatCash(splotProps.data.Price) }}
            </template>
        </Column>
        <Column field="Name_Category" header="Danh mục"></Column>
        <Column header="Hành động">
            <template #body="slotProps">
                <button type="button" class="btn btn-warning p-1 m-1" @click="updateProduct(slotProps.data)">
                    Update
                </button>
                <button class="btn btn-danger p-1 m-1" @click="deleteProduct(slotProps.data.ID_Product)">
                    Delete
                </button>
            </template>
        </Column>

        <template #footer> In total there are {{ products ? products.length : 0 }} products. </template>
    </DataTable>
    
    <div id="thongbao"></div>
    <!-- INSERT MODEL -->
    <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Insert a new product</h5>
            <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form method="POST" id='insert_form'>
                <div class="mb-3">
                    <label for="image_product" class="col-form-label">Ảnh sản phẩm đại diện:</label>
                    <input type="file" class="form-control" id="image_product" accept="image/png, image/jpeg, image/jpg" required>
                    <img id="avt" src="" style="padding: 10px; width: 85px;">
                </div>
                <div class="mb-3">
                    <label for="detail_image_product" class="col-form-label">Ảnh sản phẩm chi tiết:</label>
                    <input type="file" class="form-control" id="detail_image_product" accept="image/png, image/jpeg, image/jpg"  multiple>
                    <div class="container"></div>
                </div>
                <div class="mb-3">
                    <label for="name_product" class="col-form-label">Danh mục sản phẩm:</label>
                    <select id="category_product" class="form-select" required>
                        <!-- <?php foreach ($data['ListCategory'] as $key => $value) { ?>
                            <option value="<?=$value['ID_Category']?>"><?=$value['Name_Category']?></option>
                        <?php }?> -->
                                </select>
                </div>
                <div class="mb-3">
                    <label for="name_product" class="col-form-label">Tên sản phẩm:</label>
                    <input type='text' class="form-control" id="name_product" required/>
                </div>
                <div class="mb-3">
                    <label for="description_product" class="col-form-label">Mô tả sản phẩm:</label>
                    <textarea class="form-control" id="description_product" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="size_product" class="col-form-label">Kích thước:</label>
                    <input type='text' class="form-control" id="size_product"/>
                </div>
                <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">Chất liệu:</label>
                            </div>
                            <select class="custom-select" id="material_product">
                                <!-- <?php foreach ($data['ListMaterial'] as $key => $value) { ?>
                                    <option value="<?=$value['ID_Material']?>"><?=$value['Name_Material']?></option>
                                <?php }?> -->
                            </select>
                            <div class="container"></div>
                            <input id="material_list" type="text" hidden />
                            </div>
                <div class="mb-3">
                            <label class="form-label" for="price_product">Giá:</label>
                    <input type="number" id="price_product" class="form-control" onkeyup="EnterMoney(event)" onclick="EnterMoney(event)" required/>
                    <p></p>
                </div>
            
                <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">Màu sắc:</label>
                            </div>
                            <select class="custom-select" id="color_product">
                                <!-- <?php foreach ($data['ListColor'] as $key => $value) { ?>
                                    <option value="<?=$value['ID_Color']?>"><?=$value['Name_Color']?></option>
                                <?php }?> -->
                            </select>
                            <div class="container"></div>
                            <input id="color_list" type="text" hidden />
                            </div>

                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="insert_product">Insert</button>
                </div>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!-- UPDATE MODEL -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Update product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form method="POST" id='update_form'>
                <input type="text" class="form-control" id="id_product_update" hidden>

                <div class="mb-3">
                    <label for="image_product_update" class="col-form-label">Ảnh sản phẩm:</label>
                    <input type="file" class="form-control" id="image_product_update" accept="image/png, image/jpeg, image/jpg">
                    <img src="" style="padding: 10px; width: 85px;">
                </div>
                <div class="mb-3">
                    <label for="detail_image_product_update" class="col-form-label">Ảnh sản phẩm chi tiết:</label>
                    <input type="file" class="form-control" id="detail_image_product_update" accept="image/png, image/jpeg, image/jpg"  multiple>
                    <div></div>
                </div>
                <div class="mb-3">
                    <label for="name_product_update" class="col-form-label">Danh mục sản phẩm:</label>
                    <select id="category_product_update" class="form-select" required>
                        <!-- <?php foreach ($data['ListCategory'] as $key => $value) { ?>
                            <option value="<?=$value['ID_Category']?>"><?=$value['Name_Category']?></option>
                        <?php }?> -->
                                </select>
                </div>
                <div class="mb-3">
                    <label for="name_product_update" class="col-form-label">Tên sản phẩm:</label>
                    <input type='text' class="form-control" id="name_product_update" required/>
                </div>
                <div class="mb-3">
                    <label for="description_product_update" class="col-form-label">Mô tả sản phẩm:</label>
                    <textarea class="form-control" id="description_product_update" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="size_product_update" class="col-form-label">Kích thước:</label>
                    <input type='text' class="form-control" id="size_product_update"/>
                </div>
                <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">Chất liệu:</label>
                            </div>
                            <select class="custom-select" id="material_product_update">
                                <!-- <?php foreach ($data['ListMaterial'] as $key => $value) { ?>
                                    <option value="<?=$value['ID_Material']?>"><?=$value['Name_Material']?></option>
                                <?php }?> -->
                            </select>
                            <div class="container"></div>
                            <input id="material_list_update" type="text" hidden />
                            </div>
                <div class="mb-3">
                            <label class="form-label" for="price_product_update">Giá:</label>
                    <input type="number" id="price_product_update" class="form-control" onkeyup="EnterMoney(event)" onclick="EnterMoney(event)" required/>
                    <p></p>
                </div>
                <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">Màu sắc:</label>
                            </div>
                            <select class="custom-select" id="color_product_update">
                                <!-- <?php foreach ($data['ListColor'] as $key => $value) { ?>
                                    <option value="<?=$value['ID_Color']?>"><?=$value['Name_Color']?></option>
                                <?php }?> -->
                            </select>
                            <div class="container"></div>
                            <input id="color_list_update" type="text" hidden />
                            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="update_product">Update</button>
                </div>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!-- UPDATE .CSV MODEL -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Upload products by .csv file</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id='uploadForm'  enctype="multipart/form-data">
                    <p>Định dạng: Tiêu đề, mô tả, id category, giá</p>
                <input type="file" name="fileToUpload" id="fileToUpload">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="upload_product">Update</button>
                </div>
            </form>
        </div>
        </div>
    </div>
    </div>
</template>