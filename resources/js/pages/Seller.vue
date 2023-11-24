<script setup lang="ts">
import axios from 'axios';
import { useRoute } from 'vue-router';
import {onMounted, reactive,ref, watch} from 'vue';
import { users, user_type as user_type_interface} from '../interface';
import { LazyCodet, LazyConvert } from '../lazycodet/lazycodet';
import { setCountCard } from '../main';
import TabMenu from 'primevue/tabmenu';
import { cart_tabs } from '../tabs';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';

import Dropdown from 'primevue/dropdown';

const users = ref<users[]>();
const route = useRoute();
const isLoading = ref(false);
const isBlockUserProgressing = ref(false);
const visibleChangeLevel = ref(false);
const selectedLevel = ref(-1);
const selectedUser = ref<users>();
const user_types = ref<user_type_interface[]>();
const isChangeLevelProgressing = ref(false);
// Khai báo fillters cho chức năng tìm kiếm ở datatable
const filters = reactive({
    global: {
        value: ''
    }
} as any);
const loadData = () =>{
    isLoading.value = true;
    axios.get('/api/users/3').then(res=>{
        users.value = res.data;
        isLoading.value =false;
        
    });
}
function loadUserType()
{
    axios.post('/api/user-type').then(res=>{
        user_types.value = res.data;
    });
}
function BlockUser(user: users)
{
    isBlockUserProgressing.value = true;
    axios.post('/api/block-user', {
        idUser: user.ID_User
    }).then(res=>{
        if(res.data.status == 200)
        {   
                  // Cập nhật UI
            user.ID_UStatus = res.data.object.user__u_status.ID_UStatus;
            user.user__u_status.ID_UStatus = res.data.object.user__u_status.ID_UStatus;
            user.user__u_status.Name_UStatus = res.data.object.user__u_status.Name_UStatus;

            isBlockUserProgressing.value =false;
        }else
        {
            LazyCodet.AlertError("Đã có lỗi");
        }
      
        
    });
}
function unBlockUser(user: users)
{
    isBlockUserProgressing.value = true;
    axios.post('/api/unblock-user', {
        idUser: user.ID_User
    }).then(res=>{
        if(res.data.status == 200)
        {   
            user.ID_UStatus = res.data.object.user__u_status.ID_UStatus;
            user.user__u_status.ID_UStatus = res.data.object.user__u_status.ID_UStatus;
            user.user__u_status.Name_UStatus = res.data.object.user__u_status.Name_UStatus;
            isBlockUserProgressing.value =false;
        }else
        {
            LazyCodet.AlertError("Đã có lỗi");
        }
      
        
    });
}
function changeLevel()
{
 
    isChangeLevelProgressing.value = true;
    axios.post('/api/change-level', {
        idType: selectedLevel.value,
        idUser: selectedUser.value?.ID_User,
    }
    ).then(res=>{
        if(res.data.status == 200)
        {
            isChangeLevelProgressing.value = false;
            visibleChangeLevel.value = false;
            LazyCodet.AlertSuccess(res.data.message);
            // Cập nhật UI
            console.log(users.value);
            console.log(selectedUser.value?.ID_User);
            let index = users.value?.findIndex(u => u.ID_User == selectedUser.value?.ID_User);
            if(index !== -1)
            {
                users.value![index as any] = res.data.object;
              
            }
            
        }
        else
        {
            LazyCodet.AlertSuccess(res.data.message);
        }
    })
}
onMounted(()=>{

    loadData();
    loadUserType();
});

</script>
<template>
    <div class="p-3">
            <DataTable v-model:filters="filters" :value="users" dataKey="ID_User" tableStyle="min-width: 50rem" showGridlines stripedRows
                paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                :globalFilterFields="['Email', 'regtime']" filterDisplay="row" :loading="isLoading" 
                >
                <template #header>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <span class="text-xl text-900 font-bold">Danh sách người bán:</span>
                        <!-- <Button icon="fas fa-sync" rounded raised /> -->
                    </div>
                    <div class="d-flex justify-content-end">
                        <span class="p-input-icon-left">
                            <i class="fas fa-holly-berry" />
                            <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                        </span>
                    </div>
                </template>
                <template #loading> Loading sellers data. Please wait. </template>
                <template #empty> No sellers found. </template>

                <Column   header="ID người bán">
                    <template #body="slotProps">
                        <span>{{ slotProps.data.ID_User }}</span>
                    </template>
                </Column>
                
                <Column header="Tên người bán" sortable>
                    <template #body="slotProps">
                        <Badge v-if="slotProps.data.Name_User == null" value="Chưa cung cấp" :severity="'danger'"></Badge>
                        <span v-else>{{ slotProps.data.Name_User  }}</span>
                    </template>
                </Column>
                <Column header="Email" field="Email" sortable></Column>
                <Column header="Ngày tạo" field="regtime" sortable></Column>
                <Column header="Cập độ" field="user_type.Name_UT" sortable></Column>
                <Column header="Trạng thái" field="user__u_status.Name_UStatus" sortable>
                    <template #body="slotProps">
                        <Badge v-if="slotProps.data.user__u_status.ID_UStatus == 2" value="Đã bị khoá" :severity="'danger'"></Badge>
                        <Badge v-else value="Đang hoạt động" :severity="'success'"></Badge>
                    </template>
                </Column>
                
                
                <Column header="Hành động">
                    <template #body="splotProps">
                        <Button v-if="splotProps.data.user__u_status.ID_UStatus == 1"  type="button" @click="BlockUser(splotProps.data)" :loading="isBlockUserProgressing" class="btn btn-success">Khoá tài khoản</Button>
                        <Button v-else type="button" @click="unBlockUser(splotProps.data)" severity="secondary"  :loading="isBlockUserProgressing" class="btn btn-success">Mở tài khoản</Button>
                        <Button @click="visibleChangeLevel = true;
                             selectedLevel = splotProps.data.user_type.ID_UT;
                             selectedUser = splotProps.data;
                             " type="button" severity="warning"  class="mt-3">Đổi cấp độ</Button>
                        <Dialog v-model:visible="visibleChangeLevel"
                            :pt="{
                                mask: {
                                    style: 'backdrop-filter: blur(2px)'
                                }
                            }"
                            
                            :header="'Thay đổi cấp độ người dùng'" :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
                            <div class="d-flex justify-content-center flex-column">
                                <Dropdown v-model="selectedLevel" :options="user_types" optionLabel="Name_UT" optionValue="ID_UT"  class="w-full md:w-14rem" />
                                <br>
                                <br>
                                <Button @click="changeLevel()" :loading="isChangeLevelProgressing" class="btn btn-success">Lưu thay đổi</Button>
                            </div>
                        </Dialog>
                    </template>
                </Column>
                
                <template #footer> Tổng cộng {{ users ? users.length : 0 }} người dùng. </template>
            </DataTable>
           
    </div>
</template>
