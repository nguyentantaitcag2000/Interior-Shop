<script setup lang="ts">
import axios from 'axios';
import {ref, reactive, onMounted,computed, ComputedGetter  } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import MultiSelect from "primevue/multiselect";
import $ from 'jquery';
import Swal from 'sweetalert2';
import { LazyCodet } from '../../lazycodet/lazycodet';
interface material{
    ID_Material:number,
    Name_Material: string
}
interface color{
    ID_Color:number,
    Name_Color: string,
}
interface category{
    ID_Category: number,
    Name_Category: string
}
interface FormState{
    'ID_Product': number | null,
    'ID_Category': number | null,
    'ID_Material': number[],
    'ID_Color': number[],
    'ID_S': number | null,
    'Name_Product': string,
    'Description': string,
    'Price': number | null,
    'Avatar': string,
    'DetailImage': string[],
    'Size': string,
    'material': material | null,
    'color': color | null,
    'category': category | null
}

const products = ref([]);
const categories = ref<category[]>();
const materials = ref([]);
const colors = ref([]);
const suppliers = ref([]);
const formInsertRef = ref(null);
const mode = ref('insert'); // Chế độ <=> Sử dụng 'update' khi muốn cập nhật
const initialFormState:FormState = {
    'ID_Product': null,
    'ID_Category': null,
    'ID_Material': [],
    'ID_Color': [],
    'ID_S': null,
    'Name_Product': '',
    'Description': '',
    'Price': null,
    'Avatar': '',
    'DetailImage': [],
    'Size': '',
    'material': null,
    'color': null,
    'category' : null
};
// const form = reactive({...initialFormState});
const form = reactive<FormState>(JSON.parse(JSON.stringify(initialFormState)));
let form_update = reactive({});
const getUsers = () => {
    axios.get('/api/products').then((res)=>{
        products.value = res.data;
        
    });
    
    axios.get('/api/categories').then((res)=>{
        categories.value = res.data;
    });
    axios.get('/api/materials').then((res)=>{
        materials.value = res.data;
    });
    axios.get('/api/colors').then((res)=>{
        colors.value = res.data;
    });
    axios.get('/api/suppliers').then((res)=>{
        suppliers.value = res.data;
    });
}
const getMaterials_String = (material_obj:FormState[])=>{
    var arr = material_obj.map(
        item=>{ 
            if(item.material != null)
                return item.material.Name_Material
        });
    return arr.join(', ');
}
const getColors_String = (color_obj:FormState[])=>{
    var arr = color_obj.map(
        item=>{ 
            if(item.color != null)
                return item.color.Name_Color
        });
    return arr.join(', ');
}
// const currentForm = computed<FormState>({
//     get() {
//         return (mode.value == 'insert' ? form : form_update) as FormState;    
//     },
//     set(){}
    
// } ) 
const currentForm = computed<FormState>(() => (mode.value == 'insert' ? form : form_update) as FormState);

// Khai báo fillters cho chức năng tìm kiếm ở datatable
const filters = reactive({
    global: {
        value: ''
    }
});
const selectedCategoryDetail = computed(() => {
    if(categories.value !=undefined)
    return categories.value.find(category => category.ID_Category === form.ID_Category);
})

const InsertProduct = (event) => {
    event.preventDefault();
    LazyCodet.AlertProcessing({
        requireConfirm: false,
        workerFunction: () => {
            return axios.post('/api/InsertProduct',form).then((res)=>{
                if(res.data.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    
                    products.value.unshift(res.data.object);
                    //Reset form
                    Object.assign(form, initialFormState); // Có thể xóa được 99%, trừ 1% trường hợp là input type="file" sẽ không xóa được do nếu set input.value = '' thì sẽ lại bị lỗi validate của form require
                    formInsertRef.value.reset(); // Do lý do trên nên bắt buộc phải reset thêm cái này, cái này thì có thể reset giá trị trong input nhưng những html được tạo ra từ v-for thì nó lại không xóa được, nên kết hợp cả 2 sẽ clear full.
                    $('#insertModal').modal('hide');
                }
                else
                {
                    LazyCodet.AlertError(res.data.message);
                }
            });
        }
    })
    
}
const formatCash = (amount) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
};
const openInsertModal = () => {
    $('#form_modal').modal('show');
    mode.value = 'insert';
}
const updateProduct = (productData) => {
    $('#form_modal').modal('show');
    mode.value = 'update';
    Object.keys(productData).forEach(key => {
        if(productData[key] !== undefined)
        {
            form_update[key] = productData[key];
        }
    });
    form_update.ID_Material = productData.detail_product_material.map(item => {
        return item.material.ID_Material;
    });
    form_update.ID_Color = productData.detail_product_color.map(item => {
        return item.color.ID_Color;
    });
}
const deleteProduct = (ID_Product) => {
    LazyCodet.AlertProcessing({
        alertMessage: "Bạn có thực sự muốn xóa ?",
        workerFunction: () => {
            return axios.delete('/api/products/' + ID_Product).then((res)=>{
                if(res.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    let indexToDelete = products.value.findIndex(item => item.ID_Product === ID_Product);
                    if (indexToDelete !== -1) {
                        products.value.splice(indexToDelete, 1);
                    }
                }
                else{
                    LazyCodet.AlertError(res.data.message);
                }
                
            });
        }
    })

    
}


const onUploadImageProduct = (event) => {
    var input = event.target;
    console.log(input);
    function readFileByIndex(index)
    {
        if (!input.files || !input.files[index]) return;
            
            const FR = new FileReader();
            
            FR.addEventListener("load", function(evt) {
            if(mode.value == 'insert')
                form.Avatar = evt.target.result;
            else
                form_update.Avatar = evt.target.result;
                
            });
            FR.readAsDataURL(input.files[index]);
    }
    if(input.files.length == 1)
    {
        readFileByIndex(0);
    }
    else
    {
        for(var i = 0; i < input.files.length; i ++)
            readFileByIndex(i);
    }
}
const onUploadDetailImage = (event) => {
    var input = event.target;
    for (var i = 0; i < input.files.length; i++) {
        const reader = new FileReader();
                reader.readAsDataURL(input.files[i]);
                reader.onload = function() {
                        form.DetailImage.push(reader.result);
                        input.value='';
                }
        
    }

}
const onUploadDetailImage_Update = (event) => {
    var input = event.target;
    for (var i = 0; i < input.files.length; i++) {
        const reader = new FileReader();
                reader.readAsDataURL(input.files[i]);
                reader.onload = function() {
                        form_update.detail_product_image.push({Image:reader.result});
                        input.value='';
                }
        
    }

}
const DeleteImage = (event) => {
    var self = event.target;

    const itemToBeRemoved = self.nextElementSibling.src
    const index = form.DetailImage.indexOf(itemToBeRemoved);
    if (index !== -1) {
        form.DetailImage.splice(index, 1);
    }

    

}
const DeleteImage_Update = (event) => {
    var self = event.target;

    const itemToBeRemoved = self.nextElementSibling.src
    //Dp itemToBeRemoved có kèm theo tên domain nên phải dùng includes
    const index = form_update.detail_product_image.findIndex(item => itemToBeRemoved.includes( item.Image) );
    if (index !== -1) {
        form_update.detail_product_image.splice(index, 1);
    }

    

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
<style>
.p-dropdown-panel,.p-multiselect-panel{
    z-index: 10008 !important;
}
.p-dropdown-items-wrapper{
    max-height: 400px!important;
}
img {
    object-fit: cover;
}
</style>
<template>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" >
    <!-- <button type="button" data-bs-toggle="modal" data-bs-target="#insertModal" data-bs-whatever="@mdo" class="btn btn-success m-3">New product</button> -->
    <button @click="openInsertModal" type="button" class="btn btn-success m-3">New product</button>
    <button type="button" data-toggle="modal" data-target="#uploadModal" data-whatever="@mdo" class="btn btn-success m-3">Upload file .csv</button>
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
                <div class="row">
                    <div class="col">
                        <img width="100" height="100" :src="`${slotProps.data.Avatar.replace('/public','')}`" :alt="slotProps.data.Avatar" class="w-6rem shadow-2 border-round" />
                    </div>
                    <div class="col">
                        <div class="row"  v-for="(detail_img, index) in slotProps.data.detail_product_image">
                            <img v-if="index < 3" :src="detail_img.Image" class="img-fluid img-thumbnail" alt="Sheep" width="60">
                        </div>
                    </div>
                </div>
                
            </template>
        </Column>
        <Column field="Name_Product" header="Tên sản phẩm"></Column>
        <Column header="Màu sắc">
            <template #body="slotProps">
                <span>{{ getColors_String(slotProps.data.detail_product_color) }}</span>
            </template>
        </Column>
        <Column header="Chất liệu">
            <template #body="slotProps">
                <span>{{ getMaterials_String(slotProps.data.detail_product_material) }}</span>
            </template>
        </Column>
        
        
        <Column header="Mô tả">
            <template #body="splotProps">
                {{ splotProps.data.Description.substring(0,65) }}
            </template>
        </Column>
        <Column header="Giá" sortable sortField="Price">
            <template #body="splotProps">
                {{ formatCash(splotProps.data.Price) }}
            </template>
        </Column>
        <Column field="category.Name_Category" header="Danh mục" sortable></Column>
        <Column header="Hành động">
            <template #body="slotProps">
                <button data-toggle="modal" class="btn btn-warning p-1 m-1" @click="updateProduct(slotProps.data)">
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
    <div class="modal fade" id="form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="mode=='insert'" class="modal-title" id="exampleModalLabel">Insert a new product</h5>
                <h5 v-else class="modal-title" id="exampleModalLabel">Update a new product</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                <Button icon="fas fa-times-circle" severity="danger" data-dismiss="modal" />
            </div>
            <div class="modal-body">
                <form ref="formInsertRef" method="POST" @submit="InsertProduct">
                    <div class="mb-3">
                        <label for="image_product" class="col-form-label">Ảnh sản phẩm đại diện:</label>
                        <input type="file" class="form-control"  @change="onUploadImageProduct" accept="image/png, image/jpeg, image/jpg" required>
                        
                        <img id="avt" :src="currentForm.Avatar" style="padding: 10px; width: 85px;">
                    </div>
                    <div class="mb-3">
                        <label for="detail_image_product" class="col-form-label">Ảnh sản phẩm chi tiết:</label>

                        <input type="file" class="form-control" @change="onUploadDetailImage" accept="image/png, image/jpeg, image/jpg"  multiple>
                        <div class="row">
                            <div v-for="src in currentForm.DetailImage" class="position-relative" style="width: 100px">
                                <span @click="DeleteImage" class="close btn" style="opacity: 1;;position:absolute;background-color:#fff;color:red;right:10px;padding: 0">&times;</span>
                                <img :src="src" style="padding: 10px; width: 85px;" >    
                            </div>
                            
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="name_product" class="col-form-label">Danh mục sản phẩm:</label>
                        <div class="card flex justify-content-center">
                            <!-- <Dropdown v-model="selectedCategoty" :options="categories" optionLabel="Name_Category" placeholder="Select a City" class="w-full md:w-14rem" style="z-index: 10008;" /> -->
                            <Dropdown v-model="currentForm.ID_Category" :options="categories" optionLabel="Name_Category" optionValue="ID_Category" placeholder="Chọn 1 danh mục" class="w-full md:w-14rem">
                                <template #value="slotProps">
                                    <div v-if="selectedCategoryDetail" class="d-flex align-items-around">
                                        <img  :src="selectedCategoryDetail.Icon.replace('/public','')" style="width: 30px;" />
                                        <div class="ml-2">{{ selectedCategoryDetail.Name_Category }}</div>
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="d-flex align-items-around">
                                        <img  :src="slotProps.option.Icon.replace('/public','')" style="width: 30px" />
                                        <div class="ml-3">{{ slotProps.option.Name_Category }}</div>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                            
                    </div>
                    <div class="mb-3">
                        <label for="name_product" class="col-form-label">Tên sản phẩm:</label>
                        <input v-model="currentForm.Name_Product" type='text' class="form-control" id="name_product" required/>
                    </div>
                    <div class="mb-3">
                        <label for="description_product" class="col-form-label">Mô tả sản phẩm:</label>
                        <textarea v-model="currentForm.Description" class="form-control" id="description_product" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="size_product" class="col-form-label">Kích thước:</label>
                        <input v-model="currentForm.Size" type='text' class="form-control" id="size_product"/>
                    </div>
                    <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">Chất liệu:</label>
                                </div>
                                <!-- <Dropdown v-model="selectedMaterial" :options="materials" optionLabel="Name_Material" placeholder="Chọn chất liệu" class="w-full md:w-14rem" style="z-index: 10008;" /> -->
                                <MultiSelect v-model="currentForm.ID_Material" display="chip" :options="materials" optionLabel="Name_Material" optionValue="ID_Material" placeholder="Chọn chất liệu"
                                    :maxSelectedLabels="3" class="w-full md:w-20rem" />
                                <div class="container"></div>
                                <input id="material_list" type="text" hidden />
                    </div>
                    
                    <div class="mb-3">
                                <label class="form-label" for="price_product">Giá:</label>
                        <input v-model="currentForm.Price" type="number" id="Price" class="form-control" onkeyup="EnterMoney(event)" onclick="EnterMoney(event)" required/>
                        <p></p>
                    </div>
                
                    <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">Màu sắc:</label>
                                </div>
                                <MultiSelect v-model="currentForm.ID_Color" display="chip" :options="colors" optionLabel="Name_Color" optionValue="ID_Color" placeholder="Chọn màu sắc"
                                    :maxSelectedLabels="3" class="w-full md:w-20rem" />
                                <div class="container"></div>
                                <input id="color_list" type="text" hidden />
                    </div>
                    <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">Nhà cung cấp:</label>
                                </div>
                                <Dropdown v-model="currentForm.ID_S" :options="suppliers" optionLabel="Name_S" optionValue="ID_S" placeholder="Chọn nhà cung cấp" class="w-full md:w-14rem" style="z-index: 10008;" />
                    </div>    
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Insert</button>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="upload_product">Update</button>
                </div>
            </form>
        </div>
        </div>
    </div>
    </div>
</template>