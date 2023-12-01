<script setup lang="ts">
import axios from 'axios';
import {ref, reactive, onMounted,computed, ComputedGetter,watch  } from 'vue';
import * as XLSX from 'xlsx';
import Papa from 'papaparse';
import { saveAs } from 'file-saver';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import MultiSelect from "primevue/multiselect";
import SelectButton from 'primevue/selectbutton';
import InputMask from 'primevue/inputmask';
import $ from 'jquery';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import {material,color,category,detailProductColor,detailProductMaterial,detailProductImage,
    importHistoryDetail,detailSaleOfProduct, product_price_history as product_price_history_interface,
product,
SaleOff} from '../../interface';
import Dialog from 'primevue/dialog';
import { LoadSales } from '../../main';
import Badge from 'primevue/badge';
const tonKho_import = ref('Không xác định');
const sales = reactive<SaleOff[]>([]);
const loadingSales = ref(false);
const selectedSales = ref();
const visiblePriceHistory = ref(false);
interface FormState{
    'ID_Product': number ,
    'ID_Category': number ,
    'ID_Material': number[],
    'ID_Color': number[],
    'ID_S': number | null,
    'Name_Product': string,
    'Description': string,
    'Price': number ,
    'Avatar': string,
    'DetailImage': string[],
    'Dimensions': string[],
    'material': material ,
    'color': color ,
    'category': category ,
    'detail_product_material': detailProductMaterial[] ,
    'detail_product_color': detailProductColor[] ,
    'detail_product_image': detailProductImage[] ,
    'detail_sale_of_product': detailSaleOfProduct[],
    'product_price_history': product_price_history_interface[],
    import_history_detail: importHistoryDetail
}
const productChoosedPriceHistory = ref<FormState>();

const selectedDimensionsImport = ref<number>();
const selectedMaterialImport = ref<number>();
const selectedColorImport = ref<number>();
const priceImport = ref(1);
const amountImport = ref(1);

const products = ref<FormState[]>();
const categories = ref<category[]>();
const materials = ref([]);
const colors = ref([]);
const suppliers = ref([]);
const formInsertRef = ref<any>(null);
const mode = ref('insert'); // Chế độ <=> Sử dụng 'update' khi muốn cập nhật
const loading = ref(true);
const initialFormState:FormState = {
    'ID_Product': -1,
    'ID_Category': -1,
    'ID_Material': [],
    'ID_Color': [],
    'ID_S': -1,
    'Name_Product': '',
    'Description': '',
    'Price': 1,
    'Avatar': '',
    'DetailImage': [],
    'Dimensions': [],
    'material': {
        ID_Material: -1,
        Name_Material: ''
    },
    'color': {
        ID_Color: -1,
        Name_Color: ''
    },
    'category' : {
        Icon: '',
        ID_Category: -1,
        Name_Category: ''
    },
    'detail_product_material': [],
    'detail_product_color': [],
    'detail_product_image': [],
    'detail_sale_of_product': [],
    'product_price_history': [],
    'import_history_detail': {
        Amount:1,
        ID_IH:-1,
        ID_Product:-1,
        Price:-1

    }
};
// const form = reactive({...initialFormState});
//JSON.parse(JSON.stringify(initialFormState)) dùng cái này để làm cho initialFormState không bị thay đổi giá trị khi thay đổi các biến form
const form = reactive<FormState>(JSON.parse(JSON.stringify(initialFormState)));
let form_update = reactive<FormState>(JSON.parse(JSON.stringify(initialFormState)));
let form_import = reactive<FormState>(JSON.parse(JSON.stringify(initialFormState)));
const idSaleUpdate = ref(-1);
const getUsers = () => {
    axios.get('/api/products/_/_').then((res)=>{
        products.value = res.data;
        loading.value = false;
      
        
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
watch([selectedColorImport, selectedDimensionsImport, selectedMaterialImport], (values: [number?, number?, number?]) => {
    const [newColor, newDimensions, newMaterial] = values;
    console.log(getMaterialObject);
    console.log(getColorObject);
   
    if((getMaterialObject.value.length == 0 || newColor)  && (currentForm.value.Dimensions.length == 0 || newDimensions )  && (getMaterialObject.value.length == 0 || newMaterial)) {
        let form = {
            ID_Color:newColor,
            ID_Material:newMaterial,
            ID_D:newDimensions,
            ID_Product: form_import.ID_Product
        }
        axios.post('/api/product/amount',form).then(res=>{
            if(res.data.status == 200)
            {
                tonKho_import.value = res.data.value;
            }
            else
            {
                LazyCodet.AlertError(res.data.message);
            }
        });
    }
});

const InsertDimensions = () => {
    currentForm.value.Dimensions.push('');
}
const RemoveDimentions = (index:number) => {
    currentForm.value.Dimensions.splice(index,1);
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
const currentForm = computed<FormState>({
    get() {
        if(mode.value == 'insert') 
            return form as FormState
        else if(mode.value == 'import') 
            return form_import as FormState
        else 
            return form_update as FormState
    },
    set(){}
    
} );
const exportToCSV = () => {
  // Tạo một danh sách các dòng dữ liệu từ DataTable
  const dataRows = products.value!.map((product, index) => [
    index, // Thêm cột index
    product.ID_Product,
    product.Name_Product,
    product.category.Name_Category
    // Thêm các trường dữ liệu khác tùy theo yêu cầu
  ]);

  // Tạo một đối tượng worksheet
  const ws = XLSX.utils.aoa_to_sheet([['index', 'id_product', 'name_product', 'category', /* Các tiêu đề khác */], ...dataRows]);

  // Chuyển đổi worksheet thành CSV sử dụng papaparse
  const csvData = Papa.unparse(XLSX.utils.sheet_to_json(ws));

  // Thêm định dạng Unicode (UTF-8 BOM) vào đầu file
  const blob = new Blob(['\uFEFF' + csvData], { type: 'text/csv;charset=utf-8' });

  // Tạo và tải xuống file CSV sử dụng file-saver
  saveAs(blob, 'DanhSachSanPham.csv');
};
// const currentForm = computed<FormState>(() => (mode.value == 'insert' ? form : form_update) as FormState);

// Khai báo fillters cho chức năng tìm kiếm ở datatable
const filters = reactive({
    global: {
        value: ''
    }
} as any);
const selectedCategoryDetail = computed(() => {
    if(categories.value !=undefined)
    return categories.value.find(category => category.ID_Category === currentForm.value.ID_Category);
})
const getColorObject = computed(()=>{
    return colors.value.filter((item:any)=> currentForm.value.ID_Color.includes(item.ID_Color));
})
const getMaterialObject = computed(()=>{
    return materials.value.filter((item:any)=> currentForm.value.ID_Material.includes(item.ID_Material));
})
const SubmitForm = (event:Event) => {
    event.preventDefault();
    LazyCodet.AlertProcessing({
        requireConfirm: false,
        alertMessage: "Đang xử lý...",
        workerFunction: () => {
            let isInsert = mode.value == 'insert'; 
            let isImport = mode.value == 'import'; 
            
            let urlCall = isInsert ? '/api/InsertProduct' : '/api/UpdateProduct/' + currentForm.value.ID_Product;
            
            if(isInsert)
                urlCall = '/api/InsertProduct';
            else if(isImport)
                urlCall = '/api/ImportHistory';
            else
                urlCall = '/api/UpdateProduct/' + currentForm.value.ID_Product;
            var dataForm:any = currentForm.value;
            if(isImport)
            {
                dataForm = {
                    ...dataForm,
                    selectedColorImport: selectedColorImport.value,
                    selectedMaterialImport:selectedMaterialImport.value,
                    selectedDimensionsImport:selectedDimensionsImport.value,
                    amountImport:amountImport.value,
                    priceImport:priceImport.value
                }
            }
            return axios.post(urlCall,dataForm).then((res)=>{
                if(res.data.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    if(products.value != undefined)
                    {
                        if(isInsert)
                        {
                            products.value.unshift(res.data.object);
                        }
                        else if(isImport)
                        {
                            selectedDimensionsImport.value = undefined;
                            selectedMaterialImport.value = undefined;
                            selectedColorImport.value = undefined;
                            tonKho_import.value = 'Không xác định';
                            LazyCodet.AlertSuccess(res.data.message);
                        }
                        else{
                            let index = products.value.findIndex(item => item.ID_Product === currentForm.value.ID_Product);
                            if (index !== -1) {
                                products.value[index] = res.data.object;
                            }
                        }
                       
                        
                        //Reset form
                        Object.assign(form, initialFormState); // Có thể xóa được 99%, trừ 1% trường hợp là input type="file" sẽ không xóa được do nếu set input.value = '' thì sẽ lại bị lỗi validate của form require
                        if(formInsertRef.value)
                            formInsertRef.value.reset(); // Do lý do trên nên bắt buộc phải reset thêm cái này, cái này thì có thể reset giá trị trong input nhưng những html được tạo ra từ v-for thì nó lại không xóa được, nên kết hợp cả 2 sẽ clear full.
                        $('#form_modal').modal('hide');
                    }
                    
                }
                else
                {
                    LazyCodet.AlertError(res.data.message);
                }
            });
        }
    })
    
}

const openInsertModal = () => {
    $('#form_modal').modal('show');
    mode.value = 'insert';
}
const updateProduct = (productData:FormState) => {
    $('#form_modal').modal('show');
    mode.value = 'update';
    Object.keys(productData).forEach(key=> {
        if(productData[key as keyof FormState] !== undefined)
        {
            (form_update as any)[key] = productData[key as keyof FormState];
        }
       
        
     
    });
    form_update.Dimensions = [];
    let myProduct = (productData as any);
        myProduct.dimensions.forEach((di:any) => {
            if(products.value)
            {
                form_update.Dimensions.push(di.Name_D);    
            }
            
        });
    if(productData.detail_product_material)
        form_update.ID_Material = productData.detail_product_material.map(item => {
            return item.material.ID_Material;
        });
    if(productData.detail_product_color)
        form_update.ID_Color = productData.detail_product_color.map(item => {
            return item.color.ID_Color;
        });
    if(productData.detail_product_image)
        form_update.DetailImage = productData.detail_product_image.map(item => {
            return item.Image;
        });
}
const saleOffProduct = (productData:FormState) =>{
    $('#form_modal_saleoff').modal('show');
    idSaleUpdate.value = productData.ID_Product;

    if(productData.detail_sale_of_product)
        selectedSales.value = productData.detail_sale_of_product.map(item => {
            return item.ID_SO;
        });
     
}
const importProduct = (productData:FormState) => {
    $('#form_modal').modal('show');
    selectedDimensionsImport.value = selectedMaterialImport.value = selectedColorImport.value = undefined;
    mode.value = 'import';
    Object.keys(productData).forEach(key=> {
        if(productData[key as keyof FormState] !== undefined)
        {
            (form_import as any)[key] = productData[key as keyof FormState];
        }
    });
    form_import.Dimensions = [];
    let myProduct = (productData as any);
        myProduct.dimensions.forEach((di:any) => {
            if(products.value)
            {
                form_import.Dimensions.push(di);    
            }
            
        });
    form.import_history_detail.Price = 1;
    if(productData.detail_product_material)
        form_import.ID_Material = productData.detail_product_material.map(item => {
            return item.material.ID_Material;
        });
    if(productData.detail_product_color)
        form_import.ID_Color = productData.detail_product_color.map(item => {
            return item.color.ID_Color;
        });
    if(productData.detail_product_image)
        form_import.DetailImage = productData.detail_product_image.map(item => {
            return item.Image;
        });
}
const deleteProduct = (ID_Product:number) => {
    LazyCodet.AlertProcessing({
        alertMessage: "Bạn có thực sự muốn xóa ?",
        workerFunction: () => {
            return axios.delete('/api/products/' + ID_Product).then((res)=>{
                if(res.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    if(products.value)
                    {
                        let indexToDelete = products.value.findIndex(item => item.ID_Product === ID_Product);
                        if (indexToDelete !== -1) {
                            products.value.splice(indexToDelete, 1);
                        }
                    }
                    
                }
                else{
                    LazyCodet.AlertError(res.data.message);
                }
                
            });
        }
    })

    
}


const onUploadImageProduct = (event:any) => {
    var input = event.target;
    console.log(input);
    function readFileByIndex(index:number)
    {
        if (!input.files || !input.files[index]) return;
            
            const FR = new FileReader();
            
            FR.addEventListener("load", function(evt) {
                if(evt.target)
                {
                    let result = typeof evt.target.result == 'string' ? evt.target.result : '';
                    if(mode.value == 'insert')
                        form.Avatar = result
                    else
                        form_update.Avatar = result;
                
                }
                    
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
const onUploadDetailImage = (event: any) => {
    var input = event.target;
    for (var i = 0; i < input.files.length; i++) {
        const reader = new FileReader();
                reader.readAsDataURL(input.files[i]);
                reader.onload = function() {
                        currentForm.value.DetailImage.push(typeof reader.result == 'string' ? reader.result : '');
                        input.value='';
                }
        
    }

}
const DeleteImage = (event:any) => {
    var self = event.target;

    const itemToBeRemoved = self.nextElementSibling.src
    const index = currentForm.value.DetailImage.findIndex(item => itemToBeRemoved.includes(item));
    if (index !== -1) {
        currentForm.value.DetailImage.splice(index, 1);
    }

    

}
function SubmitFormSale(event:Event)
{
    event.preventDefault();
    console.log(selectedSales.value);
    LazyCodet.AlertProcessing({
        requireConfirm: false,
        alertMessage: "Đang xử lý...",
        workerFunction: () => {
          
            let urlCall = '/api/UpdateDetailSale';
            
           
           
            
            return axios.post(urlCall,{
                ID_Product: idSaleUpdate.value,
                id_sales: selectedSales.value
            }).then((res)=>{
                if(res.data.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    let index = products.value!.findIndex(item => item.ID_Product === idSaleUpdate.value);
                    if (index !== -1) {
                        products.value![index].detail_sale_of_product = res.data.object;
                    }
                    $('#form_modal_saleoff').modal('hide');
                    
                }
                else
                {
                    LazyCodet.AlertError(res.data.message);
                }
            });
        }
    })
}
onMounted(()=>{
    getUsers();
    LoadSales(loadingSales, sales);

});

</script>
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
    <button @click="exportToCSV" class="btn btn-primary m-3">Xuất Excel</button>

<DataTable v-model:filters="filters" :value="products" dataKey="ID_Product" tableStyle="min-width: 50rem" showGridlines stripedRows
            paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
            :globalFilterFields="['Name_Product']" filterDisplay="row" :loading="loading" 
            >
    <template #header>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span class="text-xl text-900 font-bold">Danh sách các sản phẩm:</span>
            <!-- <Button icon="fas fa-sync" rounded raised /> -->
        </div>
        <div class="d-flex justify-content-end">
            <span class="p-input-icon-left">
                <i class="fas fa-holly-berry" />
                <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
            </span>
        </div>
    </template>
    <template #empty> No products found. </template>
    <template #loading> Loading products data. Please wait. </template>
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
            {{ LazyConvert.ToMoney(splotProps.data.Price) }}
        </template>
    </Column>
    <Column field="category.Name_Category" header="Danh mục" sortable></Column>
    <Column header="Hành động">
        <template #body="slotProps">
            <button data-toggle="modal" class="btn btn-warning p-1 m-1" @click="updateProduct(slotProps.data)">
                Update
            </button>
            <button data-toggle="modal" class="btn btn-primary p-1 m-1" @click="importProduct(slotProps.data)">
                Import
            </button>
            <button data-toggle="modal" class="btn btn-success p-1 m-1" @click="saleOffProduct(slotProps.data)">
                Sale-off
            </button>
            <button class="btn btn-danger p-1 m-1" @click="deleteProduct(slotProps.data.ID_Product)">
                Delete
            </button>
            <button class="btn btn-info p-1 m-1" @click="productChoosedPriceHistory = slotProps.data; visiblePriceHistory = true">
                Price History
            </button>
        </template>
    </Column>

    <template #footer> Tổng cộng {{ products ? products.length : 0 }} sản phẩm. </template>
</DataTable>
    
    <div id="thongbao"></div>
    <!-- FORM MODEL -->
    <div class="modal fade" id="form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="mode=='insert'" class="modal-title" id="exampleModalLabel">Insert a new product</h5>
                <h5 v-else-if="mode=='import'" class="modal-title" id="exampleModalLabel">Import</h5>
                <h5 v-else class="modal-title" id="exampleModalLabel">Update a new product</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                <Button icon="fas fa-times-circle" severity="danger" data-dismiss="modal" />
            </div>
            <div class="modal-body">
                <form ref="formInsertRef" method="POST" @submit="SubmitForm">
                    <div class="mb-3">
                        <label for="name_product" class="col-form-label">Tên sản phẩm:</label>
                        <input :disabled="mode === 'import'" v-model="currentForm.Name_Product" type='text' class="form-control" id="name_product" required/>
                    </div>
                    <div v-if="mode=='import'">
                        <div class="mb-3">
                            <label for="size_product" class="col-form-label">Kích thước:</label>
                            <SelectButton v-model="selectedDimensionsImport" class="mt-3" :options="currentForm.Dimensions" optionValue="ID_D" optionLabel="Name_D"/>
                        </div>
                        <div class="mb-3">
                            <label for="size_product" class="col-form-label">Màu sắc:</label>
                            <SelectButton v-model="selectedColorImport" class="mt-3" :options="getColorObject" option-label="Name_Color" option-value="ID_Color" />
                        </div>
                        <div class="mb-3">
                            <label for="size_product" class="col-form-label">Chất liệu:</label>
                            <SelectButton v-model="selectedMaterialImport" class="mt-3" :options="getMaterialObject" option-label="Name_Material" option-value="ID_Material"  />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng:</label>
                            <input v-model="(amountImport)" type="number" class="form-control"  required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tồn kho: {{ tonKho_import }}</label>
                            
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Đơn giá:</label>
                            <input v-model="(priceImport)" type="number" class="form-control"  required/>
                        </div>
                    </div>
                    <div v-else>
                        <div class="mb-3">
                            <div class="custom-file">
                                <label for="image_product" class="custom-file-label">Ảnh sản phẩm đại diện:</label>
                                <input type="file" class="custom-file-input"  @change="onUploadImageProduct" accept="image/png, image/jpeg, image/jpg">
                            </div>
                            <img id="avt" :src="currentForm.Avatar" style="padding: 10px; width: 85px;">
                        </div>
                        <div class="mb-3">
                            <div class="custom-file">
                                <label for="detail_image_product" class="custom-file-label">Ảnh sản phẩm chi tiết:</label>
                                <input type="file" class="custom-file-input" @change="onUploadDetailImage" accept="image/png, image/jpeg, image/jpg"  multiple>
                            </div>
                            
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
                            <label for="description_product" class="col-form-label">Mô tả sản phẩm:</label>
                            <textarea v-model="currentForm.Description" class="form-control" id="description_product" required></textarea>
                        </div>
                        <!-- <div class="mb-3">
                            <label for="size_product" class="col-form-label">Kích thước:</label>
                            <input v-model="currentForm.Size" type='text' class="form-control" id="size_product"/>
                        </div> -->
                        <div class="mb-3">
                            <label for="size_product" class="col-form-label">Kích thước:</label>
                            <br>
                            <div v-for="(item,index) in currentForm.Dimensions" class="dimensions-box m-2">
                                <InputMask id="basic" v-model="currentForm.Dimensions[index]" mask="99 x 99 x 99" />
                                <Button @click="RemoveDimentions(index)" icon="fas fa-minus" class="ml-2 bg-danger"></Button>
                            </div>
                            <Button @click="InsertDimensions" icon="fas fa-plus" class="btn btn-success ml-2"></Button>
                            
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
                        
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-if="mode == 'insert'" type="submit" class="btn btn-primary">Insert</button>
                        <button v-else-if="mode == 'import'" type="submit" class="btn btn-primary">Import</button>
                        <button v-else type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
    <Dialog v-model:visible="visiblePriceHistory" modal :header="'Lịch sử giá của sản phẩm #' + productChoosedPriceHistory?.ID_Product" 
        :pt="{
            mask: {
                style: 'backdrop-filter: blur(2px)'
            }
        }"
        :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <DataTable :value="productChoosedPriceHistory?.product_price_history" 
            sortField="date_effect" :sortOrder="-1"
            tableStyle="min-width: 50rem">
            <Column field="price" header="Giá"></Column>
            <Column field="date_effect" header="Thời gian thay đổi"></Column>
            <Column field="user.Name_User" header="Người thay đổi"></Column>
            <Column field="user.ID_User" header="Mã người thay đổi"></Column>
        </DataTable>
    </Dialog>
    <!-- FORM MODEL SALEOFF -->
    <div class="modal fade" id="form_modal_saleoff" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tạo khuyến mãi</h5>
            </div>
            <div class="modal-body">
                <form ref="formInsertRef" method="POST" @submit="SubmitFormSale">
                    <div>
                        <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="inputGroupSelect01">Khuyến mãi:</label>
                                    </div>
                                    <!-- <Dropdown v-model="selectedMaterial" :options="materials" optionLabel="Name_Material" placeholder="Chọn chất liệu" class="w-full md:w-14rem" style="z-index: 10008;" /> -->
                                    <!-- <MultiSelect v-model="selectedSales" display="chip" :options="sales" optionLabel="Name_SO" optionValue="ID_SO" placeholder="Chọn khuyến mãi"
                                        :maxSelectedLabels="3" class="w-full md:w-20rem" /> -->
                                    <MultiSelect v-model="selectedSales" display="chip" :options="sales" optionLabel="Name_SO" optionValue="ID_SO"  placeholder="Chọn khuyến mãi"
                                     class="w-full md:w-20rem" :maxSelectedLabels="3" >
                                        <template #option="slotProps">
                                            <div class="d-flex align-items-center" >
                                                <div class="mr-3">{{ slotProps.option.Name_SO }}</div>
                                                <Badge :value="slotProps.option.Discount_Percent_SO + '%'"></Badge>
                                            </div>
                                        </template>
                                    </MultiSelect>
                                    <div class="container"></div>
                                    <input id="material_list" type="text" hidden />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                   
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
   
    
</template>