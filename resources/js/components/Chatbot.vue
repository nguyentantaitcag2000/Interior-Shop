<script setup lang="ts">

import {reactive,ref} from 'vue';
import Dialog from 'primevue/dialog'; 
interface Message{
    body: string,
    author: string,
}

const visible = ref(false);
const messages = reactive<Message[]>([
      {
        body: 'Welcome to the chat, I\'m Bob!',
        author: 'bob'
      },
      {
        body: 'Thank you Bob',
        author: 'you'
      },
      {
        body: 'You\'re most welcome',
        author: 'bob'
      }
    ]) 
  

</script>
<template>
    <!-- https://codepen.io/manifoldkaizen/pen/oqzOKw?__cf_chl_jschl_tk__=0defea3a284221e7dbcbd95097c0535a2da48a4b-1606610289-0-AdOgviiqRF0NoqYIcIX435L7btyFEKlkaLAOxTzOnYu12fY9H_9d5c_Z4FlTjPbNLa4tDCQ1ay-KPdbgPjs1zHNF2qqfu3XUblEAOtB0BZwD4VOcAktnzDwG1Locst2Tr7uQTmC2kd4cOshaoxmhjM4YOWUAGzKZ9C6eapzaVkQtGTcUh3eWoCxHccvXXsc5VYSVQqWWpf_2FlOSqVtpTLx-mX-wOwTsMoSo967EHK9nZsl7Dt97C8f739a8eGOMFnDWEL8g8Fw7Mt9osadFKc3C3SZyIlKZ2qyKfsbQTey7_oO0mQ84DxaHCVEfMmlRCdOpZPa664MIMmlLn4rc_3ZlcUyWGcaIm_Q6usvuqzT2qJb7V5zL0CwoZymUbxd6-dLYoK1zMZzWl-nNl_L44tA -->
    <button @click="visible = true" class="btn btn-success position-fixed" 
    style="padding:20px; bottom: 10px; right:10px; z-index: 1;"
      aria-label="Filter">
      <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M160 368c26.5 0 48 21.5 48 48v16l72.5-54.4c8.3-6.2 18.4-9.6 28.8-9.6H448c8.8 0 16-7.2 16-16V64c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16V352c0 8.8 7.2 16 16 16h96zm48 124l-.2 .2-5.1 3.8-17.1 12.8c-4.8 3.6-11.3 4.2-16.8 1.5s-8.8-8.2-8.8-14.3V474.7v-6.4V468v-4V416H112 64c-35.3 0-64-28.7-64-64V64C0 28.7 28.7 0 64 0H448c35.3 0 64 28.7 64 64V352c0 35.3-28.7 64-64 64H309.3L208 492z"/></svg>
      </button>
      
      <Dialog v-model:visible="visible" modal header="AI Bot" :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
                <p v-for="message in messages" class="message" :class="{ 'message-out': message.author === 'you', 'message-in': message.author !== 'you' }">
                {{ message.body }}
                </p>
      </Dialog>
</template>

<style scoped>

.headline {
  text-align: center;
  font-weight: 100;
  color: white;
}
.chat-area {
/*   border: 1px solid #ccc; */
  background: white;
  height: 50vh;
  padding: 1em;
  overflow: auto;
  max-width: 350px;
  margin: 0 auto 2em auto;
  box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, 0.3)
}
.message {
  width: 45%;
  border-radius: 10px;
  padding: .5em;
/*   margin-bottom: .5em; */
  font-size: 1.5em;
}
.message-out {
  background: #407FFF;
  color: white;
  margin-left: 50%;
}
.message-in {
  background: #F1F0F0;
  color: black;
}
.chat-inputs {
  display: flex;
  justify-content: space-between;
}
#person1-input {
  padding: .5em;
}
#person2-input {
  padding: .5em;  
}
</style>