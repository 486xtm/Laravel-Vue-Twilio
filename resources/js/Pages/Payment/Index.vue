<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { throttle, pickBy } from 'lodash';
import Pagination from '@/Components/Pagination.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';
import moment from "moment";
import { ref, watch, computed, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { Modal } from 'flowbite-vue'
import Alert from '@/Components/Alert.vue';
import axios from "axios";
import Multiselect from '@vueform/multiselect';  
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const breadcrumb = ref({ page:{id: 1, name: 'Contas a Receber', url : "/conta-receber"}, link:{id: 1, name: 'Pagamentos', url : "/relatorio-de-pagamentos"}});
const showModal =  ref(false);
const conciliationModal =  ref(false);
const data =  ref({});
const conciliationID =  ref(0);
const conciliationParcel =  ref("");
const noresults = ref(true)
const alert = ref(false)
const errors = ref(false)
const isLoading = ref(false);
let term = ref('');

const props = defineProps({
    payments: Object,
    filters: Object,
    status: Object,
    totals: Object,
});

const filter = reactive({
    term: props.filters.term.term,
    status: props.filters.status,
    date: props.filters.date,
});

const formUpload = useForm({
    file: null,
    date: null,
});

const form = useForm({
    id: null,
});


watch(filter,
    throttle( ()=>{
        let query = pickBy(filter);
        let queryRoute = route('payments.index', Object.keys(query).length ? query : {
            remember: 'forget',
        });
        
        router.get(
            queryRoute, 
            {},
            {
            preserveState:true,
            replace:true 
        });
    }, 500),
    {
        deep: true,
    }
);

function clearFilter(){
    router.get('/relatorio-de-pagamentos');
}

function openConciliation(id, group, quota){
    
    data.value = {};
    conciliationModal.value = true;    
    conciliationID.value = id;    
    
    getReceivables(group, quota, conciliationID);
}

async function getReceivables(group, quota, conciliationID){
    const response = await axios.get('/relatorio-de-pagamentos/conciliacao',{
        params: {
          group: group,
          quota: quota,
          conciliationID: conciliationID.value,
        }
    });    
    data.value = response.data;

    conciliationParcel.value = parseInt(data.value[0]['payment'][0]['parcel']);
    
    if(data.value[0]['receivables'].length > 0){
        noresults.value = true;
    }else{
        noresults.value = false;
    }
}


async function makeConciliation(receivableID, conciliationID){
    if (confirm("Confirma a associação deste pagamento?")) {
        isLoading.value = true;

        const response = await axios.get('/relatorio-de-pagamentos/associacao',{
            params: {
            receivableID: receivableID,
            conciliationID: conciliationID,
            }
        }).then ( function (response){
            alert.value = response.data;    
            isLoading.value = false;    
            location.reload();
        })
        .catch ( function (error){
            console.log('ajaxSearch error'); 
        });    
        
    }    
}

function closeConciliation(){
    conciliationModal.value = false;
}

function openUpload(){
    showModal.value = true;  
    errors.value = false;  
}

function closeUpload(){
    showModal.value = false;
}

function submit() {
    isLoading.value = true;
    formUpload.post('/relatorio-de-pagamentos/upload',{
        onSuccess: () => successForm(),
        onError: () => {
            console.log('erro');
            isLoading.value = false;
            errors.value = true;
        },
    });
};

function successForm(){
  showModal.value = false; 
  isLoading.value = false;
  location.reload();
}


function destroy(id) {
    if (confirm("Confirma a exclusão deste pagamento?")) {
        formUpload.delete(route('payment.destroy', id));
    }
}

function disassociation(payment) {

    if (confirm("Confirma o estorno da associação deste pagamento?")) {
        form.put(route('payment.disassociation', payment));
    }
}

const total = computed(() => {
    let totals = props.totals;
    return parseFloat(totals[0].total) + parseFloat(totals[1].total) + parseFloat(totals[2].total);    
});

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
    .multiselect-tag{
        background: #38bdf8 !important; 
    }

    .multiselect, .multiselect-option {
        font-size: var(--ms-font-size,0.9rem);
    }
    
.overlay {
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 99999;
  cursor: pointer;
}
.loader {
  position: fixed;
  left: 50%;
  top: 50%;
  width: 60px;
  height: 60px;
  margin: -76px 0 0 -76px;
  border: 8px solid #f3f3f3;
  border-radius: 50%;
  border-top: 8px solid #3498db;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}
@-webkit-keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
  }
}
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>

<template>
    <Head title="Relatório de Pagamentos" />

    <AuthenticatedLayout>
        <div v-if="isLoading" class="overlay">
            <div class="loader"></div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-4 mb-10">
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ total > 0 ? vueNumberFormat(total, { prefix: '' }) : "0,00" }}</h1>
                </div>                  
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL CONCILIADO</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[0].total > 0 ? vueNumberFormat(totals[0].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                                        
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL DIVERGENTE</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[1].total > 0 ? vueNumberFormat(totals[1].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                                        
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL NÃO IDENTIFICADO</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[2].total > 0 ? vueNumberFormat(totals[2].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                                        
            </div>

            <div class="bg-white">
                <div
                    class="relative overflow-x-auto shadow-md sm:rounded-lg min-h-[450px]"
                >   
                    <div class="grid grid-cols-6 gap-3 px-4 py-6 text-slate-600 ">
                        <div class="md:col-span-1 sm:col-span-8">
                            <TextInput
                                    type="text"
                                    name="search"
                                    v-model="filter.term"
                                    class="w-full"
                                    placeholder="Pesquisar por grupo e cota"
                                />                                
                        </div>
                        <div class="md:col-span-2 sm:col-span-8">
                            <VueDatePicker 
                                v-model="filter.date" 
                                format="dd/MM/yyyy" 
                                locale="pt-br"
                                cancelText="cancelar" 
                                selectText="selecione" 
                                :enable-time-picker="false"
                                :partial-range="true"                                
                                range 
                                utc>
                            </VueDatePicker>                            
                        </div>
                        <div class="md:col-span-2 sm:col-span-4">
                            <Multiselect
                                v-model="filter.status"
                                :options="status"
                                mode="tags"
                                placeholder="Status"
                                class="h-10"
                            />
                        </div>
                        <div class="md:col-span-1 sm:col-span-4">
                            <Button 
                                variant="default" 
                                type="button" 
                                class="ml-2"
                                @click="clearFilter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </Button>
                            <Button 
                                variant="default" 
                                type="button" 
                                class="ml-2 py-2.5"
                                title="Upload"
                                @click="openUpload">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>

                            </Button>    
                        </div>                        
                    </div>
                    <table
                        class="w-full text-sm text-left font-light text-gray-500 dark:text-gray-400 whitespace-nowrap"
                    >
                        <thead
                            class="text-slate-600 uppercase bg-slate-300 dark:bg-gray-700 dark:text-gray-400"
                        >
                            <tr>
                                <th scope="col" class="px-6 font-light py-2.5">Contrato</th>
                                <th scope="col" class="px-6 font-light">
                                    Grupo/Cota
                                </th>
                                <th scope="col" class="px-6 font-light">
                                    Parcela
                                </th>
                                <th scope="col" class="px-6 font-light">
                                    Referência
                                </th>                                
                                <th scope="col" class="px-6 font-light">
                                    Valor Base
                                </th>
                                <th scope="col" class="px-6 font-light">
                                    Comissão
                                </th>
                                <th scope="col" class="px-6 font-light">
                                    Recebido
                                </th>
                                <th scope="col" class="px-6 font-light">
                                    Status
                                </th>
                                <th scope="col" class="px-6">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="payment in payments.data"
                                :key="payment.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-slate-600"
                            >
                                <td
                                    scope="row"
                                    class="px-6 py-4"
                                >
                                    {{ payment.deal }}
                                </td>
                               <td
                                    scope="row"
                                    class="px-6 w-full"
                                >
                                    {{ payment.group }}/{{ payment.quota }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-6"
                                >
                                    {{ payment.parcel }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-6"
                                >
                                    {{ moment(payment.closed_at).format("DD/MM/YYYY") }}
                                    
                                </td>                                
                                <td
                                    scope="row"
                                    class="px-6"
                                >
                                    {{ vueNumberFormat(payment.total_deal) }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-6"
                                >
                                    {{ payment.comission }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-6"
                                >
                                    {{ vueNumberFormat(payment.total) }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    <span class="font-bold" :class="payment.status_id === 4 ? 'text-red-500':payment.status_id ===  17 ? 'text-green-500':payment.status_id === 19 ?'text-slate-400':payment.status_id === 18 ?'text-amber-400':''">
                                        {{ payment.status }}
                                    </span>    
                                </td>
                                <td class="px-4 space-x-2">
                                    <Button
                                        variant="basic"
                                        title="Conciliar"
                                        @click="openConciliation(payment.id, payment.group, payment.quota)"
                                        class="" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </Button>
                                    <Button
                                        variant="basic"
                                        v-if="payment.status_id === 17"
                                        title="Estornar"
                                        @click="disassociation(payment)"
                                        class="" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                        </svg>
                                    </Button>
                                    <Button
                                        variant="basic"
                                        v-if="payment.status_id === 19"
                                        @click="destroy(payment)"
                                        title="Excluir"
                                        class="" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="w-full bg-white justify-end py-5">
                        <Pagination :links="payments.links"></Pagination>
                    </div>
                </div>
            </div>
        </div><br><br>
        <Modal :size="size" v-if="showModal" @close="closeUpload">
            <template #header>
                <div class="flex items-center text-lg">
                Upload
                </div>
            </template>
            <template #body>
                <form @submit.prevent="submit"> 
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-3">
                            <InputLabel class="text-xs">Data de Referência</InputLabel>
                            <VueDatePicker 
                                v-model="formUpload.date" 
                                format="dd/MM/yyyy" 
                                locale="pt-br"
                                cancelText="cancelar" 
                                selectText="selecione" 
                                :enable-time-picker="false"
                                utc>
                            </VueDatePicker>
                            <div
                                v-if="formUpload.errors.date"
                                class="text-sm text-red-500 mt-2"
                            >
                                {{ formUpload.errors.date }}
                            </div>
                        </div>
                        <div class="col-span-3">
                            <input type="file" @input="formUpload.file = $event.target.files[0]" />
                            <div
                                v-if="formUpload.errors.file"
                                class="text-sm text-red-500 mt-2"
                            >
                                {{ formUpload.errors.file }}
                            </div>                        
                        </div>
                        <div v-if="$page.props.errors && errors === true" class="col-span-3 text-xs text-red-500 py-2 pr-0">
                            <div v-for="(v, k) in $page.props.errors" :key="k">  
                                <div v-if="k != 'file' && k != 'date'">
                                    <Alert type="danger">
                                        <b>Atenção:</b> {{ v }}
                                    </Alert>
                                </div>
                            </div>
                        </div>
                    </div>              
                </form>
            </template>
            <template #footer>
                <div class="flex justify-end space-x-2">
                    <Button variant="default" type="button" @click="closeUpload">
                        Cancelar
                    </Button>
                    <Button variant="primary" type="button" @click="submit">Carregar</Button>
                </div>
            </template>
            
        </Modal>

        <!-- CONCILIAÇÃO -->
        <Modal :size="size" v-if="conciliationModal" @close="closeConciliation">
            <template #header>
                <div class="flex items-center text-md">
                Conciliação de Pagamentos
                </div>
            </template>
            <template #body>
                <div v-for="object in data"
                        :key="object.id">

                    <div v-for="payment in object.payment"
                        :key="payment.id">
                        <div class="flex justify-between mb-4 border-gray-200 border p-2 px-4 bg-slate-50">
                            <span class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                                <b>Grupo/Cota:</b> {{ payment.group }}/{{ payment.quota }}
                            </span>
                            <span class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                                <b>Parcela:</b> {{ payment.parcel }}
                            </span>
                            <span class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                                <b>Fechamento:</b> {{ moment(payment.closed_at).format("DD/MM/YYYY") }}
                            </span>
                            <span class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                                <b>Total:</b> {{ vueNumberFormat(payment.total) }}
                            </span>
                        </div>                        

                        <p class="font-semibold text-xs text-slate-600">Contas a Receber:</p>
                        <div class="h-64 overflow-y-auto overflow-x-hidden">
                            <table class="w-full text-sm text-left font-light text-gray-500 dark:text-gray-400 whitespace-nowrap mt-3">
                                <thead class="text-slate-600 uppercase bg-slate-300 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 font-light py-2.5 text-xs">Parcela</th>
                                        <th scope="col" class="px-6 font-light py-2.5 text-xs">Vencimento</th>
                                        <th scope="col" class="px-6 font-light text-xs">Valor</th>
                                        <th scope="col" class="px-6 font-light text-xs">Status</th>
                                        <th scope="col" class="px-2 font-light text-xs"></th>
                                    </tr>                        
                                </thead>
                                <tbody v-if="noresults">
                                    <tr
                                        v-for="(receivable, index)  in object.receivables"
                                        :key="index"
                                        class="border-b dark:bg-gray-800 dark:border-gray-700 text-slate-600 text-xs"
                                        :class="conciliationParcel === (index+1) ? 'bg-green-100': ''"
                                    >
                                        <td scope="row" class="px-6 py-2">{{ index + 1 }}</td>
                                        <td scope="row" class="px-6">{{  moment(receivable.due_date).format("DD/MM/YYYY") }}</td>
                                        <td scope="row" class="px-6">{{ vueNumberFormat(receivable.total) }}</td>
                                        <td  scope="row" class="px-6">
                                            <span class="font-bold" :class="receivable.status_id === 4 ? 'text-red-500':receivable.status_id ===  15 ? 'text-green-500':receivable.status_id === 14 ?'text-amber-500':''">
                                                {{ receivable.status_name }}
                                            </span>        
                                        </td>                                        
                                        <td v-if="receivable.conciliation_id === null && payment.status_id != 17 " scope="row" class="px-2">
                                            <Button
                                                variant="basic"
                                                @click="makeConciliation(receivable.id, conciliationID )"
                                                class="" >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </Button>
                                        </td>
                                        <td v-else></td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr><td colspan="5" class="text-center py-2">Sem resultados</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <Alert v-if="alert" type="info" @close="onClose()" class="mt-12">
                        <p>Pagamento associado com sucesso</p>
                    </Alert>
                </div>
            </template>
            <template #footer>
                <div class="flex justify-end space-x-2">
                    <Button variant="primary" type="button" @click="closeConciliation">
                        Fechar
                    </Button>
                </div>
            </template>
        </Modal>
    </AuthenticatedLayout>
</template>
