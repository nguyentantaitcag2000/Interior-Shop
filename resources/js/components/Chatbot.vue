<script setup lang="ts">

import {reactive,ref, watch} from 'vue';
import Dialog from 'primevue/dialog'; 
import axios from 'axios';
import { useRouter } from 'vue-router';
import OverlayPanel from 'primevue/overlaypanel';
import { LazyCodet } from '../lazycodet/lazycodet';
import Button from 'primevue/button';
interface Message{
    body: string,
    author: string,
    image?: string,
}
const router = useRouter();
const visible = ref(false);
const youMessage = ref('');
const bobMessage = ref('');
const chatContainer = ref<HTMLElement|null>(null);
const showMiniPopup = ref<any>(true);
const messageMiniPopup = ref('');
const messages = reactive<Message[]>([
      {
        body: 'Chào bạn, tôi là nhân viên tư vấn AI, bạn có cần hổ trợ gì không?',
        author: 'Nhân viên',
      }
    ]);
watch(()=>visible.value, (_newVal,_oldVal)=>{
  setTimeout(()=>{
      if(_newVal && chatContainer.value)
      {
          setTimeout(()=>{
              chatContainer.value!.scrollTop = chatContainer.value!.scrollHeight;
            },0);
          
      }
  }, 0);
    
})
function sendMessage(direction:string) {
      axios.get('http://localhost:8001/message?msg=' + youMessage.value).then(res=>{
          
          let id_user = (document.getElementById('id_user_login') as HTMLInputElement).value;
          switch(res.data.intent)
          {
              case "hoi-ve-kiem-tra-tinh-trang-don-hang":
                if(id_user == "")
                {
                    messages.push({body: "Bạn muốn kiểm tra đơn hàng, giỏ hàng bằng cách nhấn vào biểu tượng Giỏ hàng, nhưng mà bạn ơi, bạn chưa đăng nhập nên sẽ không thấy được nó và tôi cũng không đưa bạn đến trang đơn hàng của bạn được, hãy đăng nhập đi nhé !", author: 'Nhân viên'} as any);
                }
                else
                {
                    messages.push({body: "Tôi sẽ đưa bạn đến đó ngay đây !", author: 'Nhân viên'} as any);
                    setTimeout(()=>{
                        router.push('/cart/processing').then(()=>{
                            window.scrollTo(0, 0);
                            messageMiniPopup.value = "Tôi đã chuyển hướng bạn đến trang xem đơn hàng rồi đó @@"
                            document.getElementById('btnMiniPopup')?.click();
                            visible.value = false;
                            setTimeout(()=>{
                                document.getElementById('btnMiniPopup')?.click();
                                

                            },4000)
                        });
                        
                        
                    },2000);
                    
                    
                }
                break;
              case 'unknown':
                if(res.data.replyFor.toLowerCase()== 'ăn cơm chưa')
                {
                    messages.push({body: '', author: 'Nhân viên', image: '/meme-bat-luc.jpg'} as any);
                    setTimeout(()=>{
                        LazyCodet.AlertSuccess("Tài khoản bạn đã bị khoá");
                    },2000);

                }
                else
                {
                    messages.push({body: res.data.message, author: 'Nhân viên'} as any);
                }
                break;
              default:
                messages.push({body: res.data.message, author: 'Nhân viên'} as any);
                break;
          }
          setTimeout(()=>{
            chatContainer.value!.scrollTop = chatContainer.value!.scrollHeight;
          },0);
          
          
      })
      if (direction === 'out') {
        messages.push({body: youMessage.value, author: 'you'} as any)
        youMessage.value = ''
      } else if (direction === 'in') {
        messages.push({body: bobMessage.value, author: 'Nhân viên'} as any)
        bobMessage.value = ''
      } else {
        alert('something went wrong')
      }
      // Cuộn xuống phần tử cuộn sau khi gửi tin nhắn
      
      if (chatContainer.value) {
        setTimeout(()=>{
          chatContainer.value!.scrollTop = chatContainer.value!.scrollHeight;
        },0);
      }
   
    }
const toggleMiniPopup = (event:any) => {
    showMiniPopup.value.toggle(event);
}
</script>
<template>
    <OverlayPanel class="bg-dark w-50 h-25 d-flex align-items-center justify-content-center" ref="showMiniPopup">
        <h4>{{ messageMiniPopup }}</h4>
    </OverlayPanel>
    <!-- https://codepen.io/manifoldkaizen/pen/oqzOKw?__cf_chl_jschl_tk__=0defea3a284221e7dbcbd95097c0535a2da48a4b-1606610289-0-AdOgviiqRF0NoqYIcIX435L7btyFEKlkaLAOxTzOnYu12fY9H_9d5c_Z4FlTjPbNLa4tDCQ1ay-KPdbgPjs1zHNF2qqfu3XUblEAOtB0BZwD4VOcAktnzDwG1Locst2Tr7uQTmC2kd4cOshaoxmhjM4YOWUAGzKZ9C6eapzaVkQtGTcUh3eWoCxHccvXXsc5VYSVQqWWpf_2FlOSqVtpTLx-mX-wOwTsMoSo967EHK9nZsl7Dt97C8f739a8eGOMFnDWEL8g8Fw7Mt9osadFKc3C3SZyIlKZ2qyKfsbQTey7_oO0mQ84DxaHCVEfMmlRCdOpZPa664MIMmlLn4rc_3ZlcUyWGcaIm_Q6usvuqzT2qJb7V5zL0CwoZymUbxd6-dLYoK1zMZzWl-nNl_L44tA -->
    <Button @click="visible = true;" class="btn btn-success position-fixed" rounded 
    style="padding:20px; bottom: 10px; right:10px; z-index: 1;"
      aria-label="Filter">
      <i class="far fa-comment-alt"></i>
      </Button>
      <button @click="toggleMiniPopup($event)" id="btnMiniPopup" class="position-fixed" style="visibility: hidden;bottom: 70px; right:70px; z-index: 1;"></button>
      <Dialog v-model:visible="visible" modal header="AI Bot" :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
          <div ref="chatContainer" class="chat-area">
              <p v-for="message in messages" class="message" :class="{ 'message-out': message.author === 'you', 'message-in': message.author !== 'you' }">
                {{ message.body }}
                <img v-if="message.image" :src="message.image" width="300" />
              </p>
              <form @submit.prevent="sendMessage('out')" id="person2-form">
                <div class="d-flex">
                  <input v-model="youMessage" class="form-control" type="text" />
                  <button class="bnt btn-primary">Gửi</button>
                </div>
              </form>
          </div>
                
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