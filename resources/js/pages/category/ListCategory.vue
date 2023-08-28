
<template>
    <h1>{{ name }}</h1>
   <DataTable :value="products" tableStyle="min-width: 50rem">
        <template #header>
            <div class="flex flex-wrap align-items-center justify-content-between gap-2">
                <span class="text-xl text-900 font-bold">Products</span>
                <Button icon="fas fa-holly-berry" rounded raised />
            </div>
        </template>
        <Column field="code" sortable  header="Code"></Column>
        <Column field="name" header="Name"></Column>
    </DataTable>
</template>
<!-- 

<script>
import axios from 'axios';
import $ from 'jquery';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
export default {
  components: {
    DataTable,
    Column
  },
  data() {
    return {
        name: 'Nguyen Van A',
        products:[{
            code: "1",
            name: "A"
        },
        {
            code: "2",
            name: "B"
        }
    ]
    };
  },
}
</script> -->


<script setup>
import { ref } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const name = ref('Nguyen Van A');
const products = [
    {
        code: "1",
        name: "A"
    },
    {
        code: "2",
        name: "B"
    }
];
setTimeout(function(){
    name.value = 'abc';
},5000);
</script>