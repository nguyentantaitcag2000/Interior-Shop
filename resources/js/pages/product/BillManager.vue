<script setup lang="ts">
import axios from 'axios';
import { useRoute } from 'vue-router';
import {onMounted, reactive,ref, watch} from 'vue';
import { order } from '../../interface';
import { LazyCodet, LazyConvert } from '../../lazycodet/lazycodet';
import { setCountCard } from '../../main';
import TabMenu from 'primevue/tabmenu';
import { cart_tabs } from '../../tabs';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';

const order = ref<order[]>();
const visibleStatusHistory = ref(false);
const billChoosedStatusHistory = ref<order>();
const route = useRoute();
const totalMoney = ref(0);
const isLoading = ref(false);
const events = ref([
    { status: 'Ordered', date: '15/10/2020 10:30', icon: 'pi pi-shopping-cart', color: '#9C27B0'},
    { status: 'Processing', date: '15/10/2020 14:00', icon: 'pi pi-cog', color: '#673AB7' },
    { status: 'Shipped', date: '15/10/2020 16:15', icon: 'pi pi-shopping-cart', color: '#FF9800' },
    { status: 'Delivered', date: '16/10/2020 10:00', icon: 'pi pi-check', color: '#607D8B' }
]);
watch(()=>route.params,()=>{
    loadBill();
})
const ChangeBillStatus = (event:Event, ord:order, id_bs:number) => {
    let button = event.target as HTMLElement;
    button.classList.add('disabled');

    axios.post('/api/bill/update/' + ord.bill.ID_Bill,{'ID_BS': id_bs}).then(res=>{
        if(res.data.status == 200)
        {

            let el = order.value?.filter(item=>item == ord)[0];
            if(el)
            {
                el = el as order;
                el.bill = res.data.object;
            }

        }
        else
        {
            LazyCodet.AlertError(res.data.message);
        }

        button.classList.remove('disabled');

    });





}
// Khai báo fillters cho chức năng tìm kiếm ở datatable
const filters = reactive({
    global: {
        value: ''
    }
} as any);
const loadBill = () =>{
    isLoading.value = true;
    axios.post('/api/order/getAll',{ID_BS: route.params.id}).then(res=>{
        order.value = res.data;
        isLoading.value =false;
        console.log(res.data);
    });
}
onMounted(()=>{

    loadBill();
});

</script>
<template>
    <div class="p-3">
            <router-link to="/admin/bill/1"   class="btn btn-link">Xem các đơn chưa thanh toán</router-link>
            <router-link to="/admin/bill/2"   class="btn btn-link">Xem các đơn đã thanh toán</router-link>
            <router-link to="/admin/bill/3"   class="btn btn-link">Xem các đơn đang vận chuyển</router-link>
            <router-link to="/admin/bill/4"   class="btn btn-link">Xem các đơn đã hủy</router-link>
            <router-link to="/admin/bill/0"   class="btn btn-link">Tất cả</router-link>
            <DataTable v-model:filters="filters" :value="order" dataKey="bill.ID_Bill" tableStyle="min-width: 50rem" showGridlines stripedRows
                paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                :globalFilterFields="['bill.ID_Bill', 'Customer_Name', 'Address_O' ]" filterDisplay="row" :loading="isLoading" 
                >
                <template #header>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <span class="text-xl text-900 font-bold">Danh sách các đơn hàng:</span>
                        <!-- <Button icon="fas fa-sync" rounded raised /> -->
                    </div>
                    <div class="d-flex justify-content-end">
                        <span class="p-input-icon-left">
                            <i class="fas fa-holly-berry" />
                            <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                        </span>
                    </div>
                </template>
                <template #loading> Loading bills data. Please wait. </template>
                <template #empty> No bills found. </template>

                <Column   header="Đơn đặt hàng">
                    <template #body="slotProps">
                        <a :href="'/bill/' + slotProps.data.bill.ID_Bill" target="_blank">#{{ slotProps.data.bill.ID_Bill }}</a>
                        
                    </template>
                </Column>
                
                <Column header="Ngày" field="bill.CreateDate" sortable></Column>
                <Column header="Tình trạng" field="bill.bill_status.Name_BS" sortable></Column>
                <Column header="Giao hàng đến" field="Address_O"></Column>
                <Column header="Khách hàng" field="Customer_Name" sortable></Column>
                <Column header="Tổng">
                    <template #body="splotProps">
                        {{ LazyConvert.ToMoney(splotProps.data.bill.TotalMoneyCheckout) }}
                    </template>
                </Column>
                
                <Column header="Hành động">
                    <template #body="splotProps">
                        <a @click="ChangeBillStatus($event,splotProps.data,2)" class="btn btn-success">Đã thanh toán</a>
                        <a @click="ChangeBillStatus($event,splotProps.data,1)" class="btn btn-light">Chưa thanh toán</a>
                        <a @click="ChangeBillStatus($event,splotProps.data,3)" class="btn btn-primary">Đang vận chuyển</a>
                        <a @click="ChangeBillStatus($event,splotProps.data,4)" class="btn btn-danger">Hủy đơn</a>
                        <a @click="billChoosedStatusHistory = splotProps.data; visibleStatusHistory = true" class="btn btn-info">Lịch sử thay đổi</a>
                    </template>
                </Column>

                <template #footer> Tổng cộng {{ order ? order.length : 0 }} đơn đặt hàng. </template>
            </DataTable>
            <Dialog v-model:visible="visibleStatusHistory" modal :header="'Lịch sử trạng thái đơn đặt hàng #' + billChoosedStatusHistory?.bill.ID_Bill" 
                :pt="{
                    mask: {
                        style: 'backdrop-filter: blur(2px)'
                    }
                }"
                :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
                <DataTable :value="billChoosedStatusHistory?.bill.bill_status_history" 
                    sortField="Date_BSH" :sortOrder="-1"
                    tableStyle="min-width: 50rem">
                    <Column field="bill_status.Name_BS" header="Trạng thái"></Column>
                    <Column field="Date_BSH" header="Thời gian thay đổi"></Column>
                    <Column field="user.Name_User" header="Người thay đổi"></Column>
                    <Column field="user.ID_User" header="Mã người thay đổi"></Column>
                </DataTable>
            </Dialog>
    </div>
</template>
