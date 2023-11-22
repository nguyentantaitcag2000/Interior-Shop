<script setup lang="ts">
import axios from 'axios';
import InputText from 'primevue/inputtext';
import {ref} from 'vue';
import { LazyCodet } from '../lazycodet/lazycodet';
import { users } from '../interface';
import Textarea from 'primevue/textarea';

const id_user = (document.getElementById('id_user_login') as HTMLInputElement).value;
const user = ref<users>();


axios.post('/api/user', {idUser: id_user}).then(res=>{
    if(res.data.status == 200)
    {
        user.value = res.data.object;
    }   
    else
    {
        LazyCodet.AlertError(res.data.message);
    }
});
function Save()
{
    LazyCodet.AlertProcessing({
        requireConfirm: false,
        workerFunction() {
            return axios.post('/api/saveProfile', {
                idUser: id_user,
                user:user.value
            }).then(res=>{
                console.log(res.data);
                if(res.data.status== 200)
                {
                    LazyCodet.AlertSuccess(res.data.message);
                }
                else
                {
                    LazyCodet.AlertError(res.data.message);
                }
            });
        },
    })
}
</script>
<template>
    <div class="p-3 d-flex flex-column mb-5">
        <h1>Thông tin cá nhân</h1>
        <div class="d-flex justify-content-center mt-5">
    
            <form v-if="user" class="p-3 card w-50">
                <span class="p-float-label">
                    <InputText :disabled="true" v-model="user.Email" class="w-100" />
                    <label>Email</label>
                </span>
                <br>
                <br>
                <span class="p-float-label">
                    <InputText v-model="user.Name_User" class="w-100" />
                    <label>Họ tên</label>
                </span>
                <br>
                <br>
                <span class="p-float-label">
                    <InputText v-model="user.Phone" class="w-100" />
                    <label>Số điện thoại</label>
                </span>
                <br>
                <br>
                <span class="p-float-label">
                    <Textarea v-model="user.Address" rows="5" cols="30" class="w-100" />
                    <label>Địa chỉ</label>
                </span>
            </form>
        </div>
        <button @click="Save()" class="btn btn-primary">Lưu</button>
    </div>
</template>