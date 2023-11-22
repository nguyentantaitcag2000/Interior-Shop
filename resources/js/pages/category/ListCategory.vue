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

interface category{
    ID_Category: number,
    Name_Category: string,
    Icon:string
}

interface FormState{
    'ID_Category': number | null,
    'Name_Category': string,
    'Icon': string,
}

const categories = ref<category[]>();
const materials = ref([]);
const colors = ref([]);
const suppliers = ref([]);
const formInsertRef = ref<any>(null);
const mode = ref('insert'); // Chế độ <=> Sử dụng 'update' khi muốn cập nhật
const loading = ref(true);
const initialFormState:FormState = {
    'ID_Category': null,
    'Name_Category': '',
    'Icon': '',
};
const form = reactive<FormState>(JSON.parse(JSON.stringify(initialFormState)));
let form_update = reactive<FormState>(JSON.parse(JSON.stringify(initialFormState)));


const getData = () => {
    axios.get('/api/categories').then((res)=>{
        categories.value = res.data;
        loading.value = false;
    });
 
}
const currentForm = computed<FormState>(() => (mode.value == 'insert' ? form : form_update) as FormState);

// Khai báo fillters cho chức năng tìm kiếm ở datatable
const filters = reactive({
    global: {
        value: ''
    }
} as any);

const SubmitForm = (event:any) => {
    event.preventDefault();
    LazyCodet.AlertProcessing({
        requireConfirm: false,
        alertMessage: "Đang xử lý...",
        workerFunction: () => {
            let isInsert = mode.value == 'insert'; 
            let urlCall = isInsert ? '/api/InsertCategory' : '/api/UpdateCategory/' + currentForm.value.ID_Category;
            return axios.post(urlCall,currentForm.value).then((res)=>{
                if(res.data.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    if(categories.value != undefined)
                    {
                        if(isInsert)
                        {
                            categories.value.unshift(res.data.object);
                        }
                        else{
                            let index = categories.value.findIndex(item => item.ID_Category === currentForm.value.ID_Category);
                            if (index !== -1) {
                                categories.value[index] = res.data.object;
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

}
const deleteData = (ID:number) => {
    LazyCodet.AlertProcessing({
        alertMessage: "Bạn có thực sự muốn xóa ?",
        workerFunction: () => {
            return axios.delete('/api/delete/category/' + ID).then((res)=>{
                if(res.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    if(categories.value)
                    {
                        let indexToDelete = categories.value.findIndex(item => item.ID_Category === ID);
                        if (indexToDelete !== -1) {
                            categories.value.splice(indexToDelete, 1);
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
                        form.Icon = result
                    else
                        form_update.Icon = result;
                
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
onMounted(()=>{
    getData();
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
    <button @click="openInsertModal" type="button" class="btn btn-success m-3">New category</button>
    <DataTable v-model:filters="filters" :value="categories" dataKey="ID_Category" tableStyle="min-width: 50rem" showGridlines stripedRows
                paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                :globalFilterFields="['Name_Category']" filterDisplay="row" :loading="loading" 
                >
        <template #header>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span class="text-xl text-900 font-bold">Danh sách các danh mục:</span>
                <!-- <Button icon="fas fa-sync" rounded raised /> -->
            </div>
            <div class="d-flex justify-content-end">
                <span class="p-input-icon-left">
                    <i class="fas fa-holly-berry" />
                    <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                </span>
            </div>
        </template>
        <template #empty> No categories found. </template>
        <template #loading> Loading categories data. Please wait. </template>
        <Column header="Biểu tượng">
            <template #body="slotProps">
                <div class="row">
                    <div class="col">
                        <img  :src="`${slotProps.data.Icon.replace('/public','')}`" :alt="slotProps.data.Icon" class="w-6rem shadow-2 border-round" />
                    </div>
                </div>
                
            </template>
        </Column>
        <Column field="Name_Category" header="Tên danh mục"></Column>
        <Column header="Hành động">
            <template #body="slotProps">
                <button data-toggle="modal" class="btn btn-warning p-1 m-1" @click="updateProduct(slotProps.data)">
                    Update
                </button>
             
                <button class="btn btn-danger p-1 m-1" @click="deleteData(slotProps.data.ID_Category)">
                    Delete
                </button>
            </template>
        </Column>

        <template #footer> Tổng cộng {{ categories ? categories.length : 0 }} danh mục. </template>
    </DataTable>
    
    <div id="thongbao"></div>
    <!-- FORM MODEL -->
    <div class="modal fade" id="form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="mode=='insert'" class="modal-title" id="exampleModalLabel">Thêm 1 danh mục mới</h5>
                <h5 v-else class="modal-title" id="exampleModalLabel">Cập nhật danh mục</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                <Button icon="fas fa-times-circle" severity="danger" data-dismiss="modal" />
            </div>
            <div class="modal-body">
                <form ref="formInsertRef" method="POST" @submit="SubmitForm">
                    <div class="mb-3">
                        <label for="image_product" class="col-form-label">Biểu tượng:</label>
                        <input type="file" class="form-control"  @change="onUploadImageProduct" accept="image/png, image/jpeg, image/jpg">
                        
                        <img id="avt" :src="currentForm.Icon" style="padding: 10px; width: 85px;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="name_product" class="col-form-label">Tên danh mục:</label>
                        <input v-model="currentForm.Name_Category" type='text' class="form-control" id="name_product" required/>
                    </div>
                    
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-if="mode == 'insert'" type="submit" class="btn btn-primary">Insert</button>
                        <button v-else type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
   
    
</template>