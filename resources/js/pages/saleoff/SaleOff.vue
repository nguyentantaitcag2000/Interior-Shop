<script setup lang="ts">
import axios from 'axios';
import Calendar from 'primevue/calendar';
import InputNumber from 'primevue/inputnumber';

import {ref, reactive} from 'vue';
import { LazyCodet } from '../../lazycodet/lazycodet';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import $ from 'jquery';
import { format } from 'date-fns';
import { LoadSales } from '../../main';
import { SaleOff } from '../../interface';



const startDate = ref(getFormattedDate());
const endDate = ref("");

const startDate_update = ref<string| Date>(getFormattedDate());
const endDate_update = ref<string| Date>("");

const sales = reactive<SaleOff[]>([]);
const loadingSales = ref(false);
let idUpadteSale: number = -1;

const initialFormState:SaleOff = {
        name: "",
        percent: 10,
        endTimestamp: 0,
        startTimestamp: 0,
    }
const form = reactive<SaleOff>(JSON.parse(JSON.stringify(initialFormState)));

let form_update = reactive<SaleOff>(JSON.parse(JSON.stringify(initialFormState)));
    function convertTimestampToCalendarDate(timestamp) {
      // Chuyển đổi timestamp thành chuỗi định dạng ISO 8601
      const isoDate = new Date(timestamp).toISOString();

      // Lấy phần đầu của chuỗi (yyyy-MM-dd) và chuyển đổi thành Date object
      return new Date(isoDate.substring(0, 10));
    }

function SubmitForm(event:Event, isUpdate: boolean= false)
{
    event.preventDefault();
    let myForm = isUpdate ? form_update : form;
    let url = isUpdate ? '/api/updateSaleOff/' + idUpadteSale : '/api/createSaleOff';
    if(isUpdate)
    {
        myForm.startTimestamp = convertToTimestamp(form_update.startTimestamp)!;
        myForm.endTimestamp = convertToTimestamp(form_update.endTimestamp)!;
    }
    else{
        myForm.startTimestamp = convertToTimestamp(startDate.value)!;
        myForm.endTimestamp = convertToTimestamp(endDate.value)!;
    }
    
    if(myForm.percent > 100 || myForm.percent <0 )
    {
        LazyCodet.AlertError("Giá trị phần trăm khuyến mãi không phù hợp");
        return;
    }
    if(myForm.startTimestamp > myForm.endTimestamp )
    {
        LazyCodet.AlertError("Giá trị thời gian bắt đầu lớn hơn thời gian kết thúc");
        return;
    }
    if(myForm.name.trim().length == 0 )
    {
        LazyCodet.AlertError("Giá trị tên không được rỗng");
        return;
    }
  
    
        LazyCodet.AlertProcessing({
            requireConfirm: false,
            workerFunction:function(){
                return axios.post(url,myForm).then(res=>{
                if(res.data.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                    if(isUpdate)
                    {
                        let index = sales.findIndex(item => item.ID_SO == idUpadteSale.toString());
                        if (index !== -1) {
                            sales[index] = res.data.object;
                        }
                        $('#form_modal').modal('hide');

                    }
                    else
                    {
                        Object.assign(sales,[res.data.sale, ...sales]);
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
const updateSale = (productData:SaleOff) => {
    idUpadteSale = parseInt(productData.ID_SO!);
    $('#form_modal').modal('show');
    Object.keys(productData).forEach(key=> {
        if(productData[key as keyof SaleOff] !== undefined)
        {
            let keyForm = key;
            let value: string | number | undefined | Date = productData[key as keyof SaleOff];
            if(key == 'Name_SO')
            {
                keyForm = "name";
            }
            else if(key == 'Start_Date_SO')
            {
                keyForm = "startTimestamp";
                value = convertTimestampToCalendarDate(value!);
                startDate_update.value = value;
            }
            else if(key == 'End_Date_SO')
            {
                keyForm = "endTimestamp";
                value = convertTimestampToCalendarDate(value!);
                endDate_update.value = value;
            }
           

            (form_update as any)[keyForm] = value;
        }
     
    });
}
const deleteSale = (ID_Sales:string) => {
    LazyCodet.AlertProcessing({
        alertMessage: "Bạn có thực sự muốn xóa ?",
        workerFunction: () => {
            return axios.delete('/api/sales/' + ID_Sales).then((res)=>{
                if(res.status == 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                
                    let indexToDelete = sales.findIndex(item => item.ID_SO === ID_Sales);
                    if (indexToDelete !== -1) {
                        sales.splice(indexToDelete, 1);
                    }
                    
                    
                }
                else{
                    LazyCodet.AlertError(res.data.message);
                }
                
            });
        }
    })

    
}



function convertToTimestamp(dateInput) {
  const timestamp = Date.parse(dateInput);
  return isNaN(timestamp) ? null : timestamp;
}
function getFormattedDate() {
  const today = new Date();
  const day = today.getDate().toString().padStart(2, '0');
  const month = (today.getMonth() + 1).toString().padStart(2, '0'); // Tháng bắt đầu từ 0
  const year = today.getFullYear();

  return `${month}/${day}/${year}`;
}

LoadSales(loadingSales, sales);
</script>
<template>
    <div class="p-3">
        <form id="form" @submit="SubmitForm">

            <div class="d-flex align-items-center mb-3 pb-1">
                <img src="/sale.png"/>
                <!-- <span class="h1 fw-bold mb-0">Logo</span> -->
            </div>

            <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Tạo khuyến mãi</h5>

            <div class="form-outline mb-4">
                <label class="form-label" for="email">Tên khuyến mãi</label>
                <input v-model="form.name" type="text" id="email" class="form-control form-control-lg" required/>
            </div>
         
            <div class="form-outline mb-4">
                <label class="form-label" for="password">Ngày bắt đầu</label><br>
                <Calendar v-model="startDate" />
            </div>
            <div class="form-outline mb-4">
                <label class="form-label" for="password">Ngày kết thúc</label><br>
                <Calendar v-model="endDate" />

            </div>
            <div class="form-outline mb-4">
                <label class="form-label" for="password">Phần trăm khuyến mãi</label><br>
                <InputNumber v-model="form.percent" inputId="withoutgrouping" :useGrouping="false" prefix="%"/>


            </div>

            <div class="pt-1 mb-4">
                <button class="btn btn-dark btn-lg btn-block" type="submit">Tạo khuyến mãi</button>
            </div>


        </form>
        <DataTable :value="sales" dataKey="ID_SO" tableStyle="min-width: 50rem" showGridlines stripedRows
                paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                :globalFilterFields="['Name_SO']" filterDisplay="row" :loading="loadingSales" 
                >
            <template #header>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="text-xl text-900 font-bold">Danh sách các khuyến mãi:</span>
                </div>
            
            </template>
            <template #empty> No sales found. </template>
            <template #loading> Loading sales data. Please wait. </template>
            <Column field="ID_SO" sortable  header="Code"></Column>
            <Column field="Name_SO" header="Tên khuyến mãi"></Column>
        
            <Column header="Phần trăm" sortable sortField="Discount_Percent_SO">
                <template #body="splotProps">
                    {{ splotProps.data.Discount_Percent_SO }}
                </template>
            </Column>
            <Column field="Start_Date_SO" header="Ngày bắt đầu" sortable></Column>
            <Column field="End_Date_SO" header="Ngày kết thúc" sortable></Column>
            <Column header="Hành động">
                <template #body="slotProps">
                    <button data-toggle="modal" class="btn btn-warning p-1 m-1" @click="updateSale(slotProps.data)">
                        Update
                    </button>
                    <button class="btn btn-danger p-1 m-1" @click="deleteSale(slotProps.data.ID_SO)">
                        Delete
                    </button>
                </template>
            </Column>

            <template #footer> Tổng cộng {{ sales ? sales.length : 0 }} khuyến mãi. </template>
        </DataTable>
        <div class="modal fade" id="form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update</h5>
                
                </div>
                <div class="modal-body">
                    <form id="form" @submit="SubmitForm($event, true)">
                        <div class="d-flex align-items-center mb-3 pb-1">
                            <img src="/sale.png"/>
                            <!-- <span class="h1 fw-bold mb-0">Logo</span> -->
                        </div>

                        <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Cập nhật khuyến mãi</h5>

                        <div class="form-outline mb-4">
                            <label class="form-label" for="email">Tên khuyến mãi</label>
                            <input v-model="form_update.name" type="text" id="email" class="form-control form-control-lg" required/>
                        </div>

                        <div class="form-outline mb-4">
                            <label class="form-label" for="password">Ngày bắt đầu</label><br>
                            <Calendar style="z-index: 99999;" v-model="startDate_update" />
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="password">Ngày kết thúc</label><br>
                            <Calendar id="calendar-13h" v-model="endDate_update" />

                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="password">Phần trăm khuyến mãi</label><br>
                            <InputNumber v-model="form_update.percent" inputId="withoutgrouping" :useGrouping="false" prefix="%"/>


                        </div>

                        <div class="pt-1 mb-4">
                            <button class="btn btn-dark btn-lg btn-block" type="submit">Cập nhật khuyến mãi</button>
                        </div>


                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
    
</template>
<style>
        .p-datepicker{
            z-index: 9990000099 !important;
        }
    </style>