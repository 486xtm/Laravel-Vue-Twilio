<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { throttle, pickBy } from 'lodash';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import Pagination from '@/Components/Pagination.vue';
import NewReceivable from './Partials/Create.vue';
import Button from '@/Components/Button.vue';
import Alert from '@/Components/Alert.vue';
import SlideOver from '@/Components/SlideOver.vue';
import Link from '@/Components/Link.vue';
import { ref, watch, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import moment from "moment";
import Multiselect from '@vueform/multiselect';  
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import '@vuepic/vue-datepicker/dist/main.css' 

const breadcrumb = ref({ page:{id: 1, name: 'Contas a Receber', url : "/conta-receber"}});

const props = defineProps({
    receivables: Object,
    users: Object,
    status: Object,
    origens: Object,
    filters: Object,
    totals: Object,
});

const filter = reactive({
    term: props.filters.term.term,
    status: props.filters.status,
    origem: props.filters.origem,
    date: props.filters.date,
    due_date: props.filters.due_date,
    user: props.filters.user,
});

const updateTab = ref(false);
const success = ref(false);
const config = {
    thousands: '.',
    decimal: ',',
    precision: 2,
    disableNegative: false,
    disabled: false,
    allowBlank: false,
    minimumNumberOfCharacters: 0,
    shouldRound: false,
    focusOnRight: false,
    modelModifiers: {
        number: true,
    },
};
const filterTab = ref(false);
const newTab = ref(false);

const formUpdate = useForm({
    id: null,
    group: null,
    quota: null,
    description: null,
    due_date: null,
    subtotal: null,
    descount: null,
    interest: null,
    total: null,
});

const form = useForm({
    id: null,
});

watch(filter,
    throttle( ()=>{
        let query = pickBy(filter);
        let queryRoute = route('receivable.index', Object.keys(query).length ? query : {
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

function exportReceivables(){
    let query = pickBy(filter);
    let queryRoute = route('receivable.export', Object.keys(query).length ? query : {
        remember: 'forget',
    });

    window.open(queryRoute);
}


function clearFilter(){
    router.get('/conta-receber');
}

function openFilter(){
    filterTab.value = true;
}

function closeFilter(){
    filterTab.value = false;
}

function closeSlide(){
    newTab.value = false;
}

function newReceivable(){
    newTab.value = true;
}

function openUpdate(receivable){
    updateTab.value = true;
    success.value = false;
    
    formUpdate.id = receivable.id;
    formUpdate.group = receivable.group;
    formUpdate.quota = receivable.quota;
    formUpdate.description = receivable.description;
    formUpdate.due_date = receivable.due_date;
    formUpdate.subtotal = receivable.subtotal;
    formUpdate.descount = receivable.descount;
    formUpdate.interest = receivable.interest;
    formUpdate.total = receivable.total;

}

function calc(){    
    console.log(formatNumber(formUpdate.subtotal));
    formUpdate.total = formatNumber(formUpdate.subtotal)+formatNumber(formUpdate.interest)-formatNumber(formUpdate.descount);
}

function formatNumber(p){
    var n = p.replace('.','');
    n = n.replace(',','.');    
    return parseFloat(n);
}

function closeUpdate(){
    updateTab.value = false;
}

function updateReceivable(errors){
    formUpdate.put(route("receivable.update", formUpdate.id),{
        onSuccess: () => successForm(),
    });    
}

function successForm(){
    success.value = true;
    formUpdate.reset();
}

function destroy(id) {
    if (confirm("Confirma a exclusão desta conta a receber?")) {
        form.delete(route('receivable.destroy', id));
    }
}

function approve(proposal) {
    if (confirm("Confirma a baixa dessa conta a receber?")) {
        form.put(route('receivable.approve', proposal.id));
    }
}

function unpprove(id) {
    if (confirm("Confirma o estorno da aprovação desta proposta?")) {
        form.put(route('receivable.unpprove', id));
    }
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
    .multiselect-tag{
        background: #38bdf8 !important; 
    }
</style>
<template>
    <Head title="Contas a Receber" />  

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            
            <Alert v-if="$page.props.flash.message" type="info" @close="onClose()" class="mb-8">
                {{ $page.props.flash.message }}
            </Alert> 

            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-4 mb-10">
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL ABERTO</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[0].total > 0 ? vueNumberFormat(totals[0].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL ATRASADO</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[1].total > 0 ? vueNumberFormat(totals[1].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL RECEBIDO</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[2].total > 0 ? vueNumberFormat(totals[2].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                
            </div>

            <div class="bg-white">
                <div
                    class="relative overflow-x-auto shadow-md sm:rounded-lg min-h-[450px]"
                >   
                    <div class="grid md:grid-cols-7 sm:grid-cols-4 gap-3 px-4 py-6 text-slate-600 ">
                        <div class="col-span-1">
                            <Button
                                variant="primary"
                                class="block h-9 w-full"
                                @click="newReceivable()">
                                NOVA CONTA 
                            </Button>
                        </div>
                        <div class="col-span-3">
                            <TextInput
                                    type="text"
                                    name="search"
                                    v-model="filter.term"
                                    class="w-full"
                                    placeholder="Pesquise por nome"
                                />
                        </div>
                        <div class="md:col-span-2 sm:col-span-8 sm:hidden md:block">
                            <VueDatePicker 
                                v-model="filter.due_date" 
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
                        <div class="flex">
                            <Button 
                                variant="default" 
                                type="button" 
                                class="ml-0"
                                @click="openFilter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-sky-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                                </svg>
                            </Button>
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
                                class="ml-2 h-9 py-0.5 md:flex"
                                title="Exportar para Excel"
                                @click="exportReceivables">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
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
                                <th scope="col" class="px-4 py-2.5 w-2">#</th>
                                <th scope="col" class="px-4 font-light">
                                    Descrição
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Emissão
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Vencimento
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Valor
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Status
                                </th>
                                <th scope="col" class="font-light"></th>
                                
                                <th scope="col" class="px-4 font-light">
                                    Usuário
                                </th>
                                <th scope="col" class="px-6 w-64">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="receivable in receivables.data"
                                :key="receivable.id"
                                class="bg-white border-b border-slate-100  text-slate-600 even:bg-slate-50 odd:bg-white"
                            >
                                <td
                                    scope="row"
                                    class="px-4 py-4"
                                >
                                    <input type="checkbox" :id="`chk_${receivable.id}`" v-model="checked" />   
                                </td>
                                <td
                                    scope="row"
                                    class="px-4 w-full"
                                >
                                    {{ receivable.description }} 
                                </td>                                
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    {{ moment(receivable.created_at).format("DD/MM/YYYY") }}
                                </td>          
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    {{ moment(receivable.due_date).format("DD/MM/YYYY") }}
                                </td>                                 
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    {{ vueNumberFormat(receivable.total) }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    <span class="font-bold" :class="receivable.status_id === 4 ? 'text-red-500':receivable.status_id ===  15 ? 'text-green-500':receivable.status_id === 14 ?'text-amber-500':''">
                                        {{ receivable.status_name }}
                                    </span>    
                                </td>
                                <td
                                    scope="row"
                                    class=""
                                >
                                    <span class="flex font-bold w-4 h-4 rounded-lg" :class="receivable.conciliation_id != null ? 'bg-green-400': 'text-green-500'" title="Conciliado"></span>    
                                </td>
                                <td 
                                    scope="row"
                                    class="px-4 whitespace-nowrap"
                                >
                                    {{ receivable.usuario }}
                                </td>
                                <td class="flex space-x-2 py-3 px-4">
                                    <Button
                                        variant="basic"
                                        @click="openUpdate(receivable)"                                        
                                        title="Editar" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </Button>
                                    <Button
                                        v-if="receivable.status_id === 4 || receivable.status_id === 14"    
                                        variant="basic"
                                        @click="approve(receivable)"
                                        class="" 
                                        title="Aprovar" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                                        </svg>
                                    </Button>
                                    <Button
                                        v-if="receivable.status_id === 4 || receivable.status_id === 14"    
                                        variant="basic"
                                        @click="destroy(receivable)"
                                        class="" 
                                        title="Excluir" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </Button>
                                    <Button
                                        v-if="receivable.status_id === 15"    
                                        variant="basic"
                                        @click="unpprove(receivable)"
                                        class="" 
                                        title="Estornar" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                        </svg>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="w-full bg-white justify-end py-5">
                        <Pagination :links="receivables.links"></Pagination>
                    </div>
                </div>
            </div>
        </div><br><br>

        <!-- FILTER -->
        <SlideOver :showSlide="filterTab" title="Filtro Avançado" @close="closeFilter()">
            <div>
                <div class="bg-white">
                    <form @submit.prevent="filterLeads">
                        <div class="p-5 py-5 space-y-5">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Contas Criadas</InputLabel>
                                    <VueDatePicker 
                                        v-model="filter.date" 
                                        format="dd/MM/yyyy" 
                                        locale="pt-br"
                                        cancelText="cancelar" 
                                        selectText="selecione" 
                                        :enable-time-picker="false"
                                        :partial-range="false"
                                        range 
                                        utc>
                                    </VueDatePicker>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Contas Baixadas</InputLabel>
                                    <VueDatePicker 
                                        v-model="filter.approved" 
                                        format="dd/MM/yyyy" 
                                        locale="pt-br"
                                        cancelText="cancelar" 
                                        selectText="selecione" 
                                        :enable-time-picker="false"
                                        :partial-range="false"
                                        range 
                                        utc>
                                    </VueDatePicker>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Status</InputLabel>
                                    <Multiselect
                                        v-model="filter.status"
                                        :options="status"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Usuários</InputLabel>
                                    <Multiselect
                                        v-model="filter.user"
                                        :options="users"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div> 
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Origem</InputLabel>
                                    <Multiselect
                                        v-model="filter.origem"
                                        :options="origens"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>                            
                        </div>
                    </form>
                </div>  
            </div>
        </Slideover>

        <!-- UPDATE -->
        <SlideOver :showSlide="updateTab" title="Conta à Receber" @close="closeUpdate()">
            <div>
                <div v-if="!success" class="bg-white">
                    <form @submit.prevent="updateReceivable">
                        <div class="p-5 py-5 space-y-5">
                            <div class="mt-1 grid grid-cols-2 space-x-2">
                                <div>
                                    <TextInput
                                        textLabel="Grupo"
                                        type="text" 
                                        v-model="formUpdate.group"
                                        name="group"
                                        class="w-full"
                                        placeholder=""
                                        disabled="true"                               
                                    />
                                    <div
                                        v-if="formUpdate.errors.group"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ formUpdate.errors.group }}
                                    </div>
                                </div>
                                <div>
                                    <TextInput
                                        textLabel="Cota"
                                        type="text"
                                        v-model="formUpdate.quota"
                                        name="quota"
                                        class="w-full"
                                        placeholder=""    
                                        disabled="true"                                    
                                    />
                                    <div
                                        v-if="formUpdate.errors.quota"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ formUpdate.errors.quota }}
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <TextInput
                                        textLabel="Descrição"
                                        type="text" 
                                        v-model="formUpdate.description"
                                        name="description"
                                        placeholder=""
                                        disabled="true"                                
                                    />
                                    <div
                                        v-if="formUpdate.errors.description"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ formUpdate.errors.description }}
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Vencimento</InputLabel>
                                    <VueDatePicker 
                                        v-model="formUpdate.due_date" 
                                        format="dd/MM/yyyy" 
                                        locale="pt-br"
                                        cancelText="cancelar" 
                                        selectText="selecione" 
                                        :enable-time-picker="false"
                                        utc>
                                    </VueDatePicker>
                                    <div
                                        v-if="formUpdate.errors.due_date"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ formUpdate.errors.due_date }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label
                                    for="textLabel"
                                    class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                    >Valor
                                </label>
                                <input
                                    class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                    v-model.lazy="formUpdate.subtotal"
                                    name="subtotal"
                                    v-money3="config"
                                    placeholder="0,00"
                                    @blur="calc"                                                                           
                                />
                                <div
                                    v-if="formUpdate.errors.subtotal"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ formUpdate.errors.subtotal }}
                                </div>
                            </div>
                            <div>
                                <label
                                    for="textLabel"
                                    class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                    >Desconto (R$)
                                </label>
                                <input
                                    class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                    v-model.lazy="formUpdate.descount"
                                    name="descount"
                                    v-money3="config"
                                    placeholder="0,00"
                                    @blur="calc"                                                                           
                                />
                                <div
                                    v-if="form.errors.descount"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.descount }}
                                </div>
                            </div>
                            <div>
                                <label
                                    for="textLabel"
                                    class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                    >Juros (R$)
                                </label>
                                <input
                                    class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                    v-model.lazy="formUpdate.interest"
                                    name="interest"
                                    v-money3="config"
                                    placeholder="0,00"
                                    @blur="calc"                                                                           
                                />
                                <div
                                    v-if="form.errors.interest"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.interest }}
                                </div>
                            </div>
                            <div>
                                <label
                                    for="textLabel"
                                    class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                    >Total
                                </label>
                                <input
                                    class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                    v-model.lazy="formUpdate.total"
                                    name="total"
                                    v-money3="config"
                                    placeholder="0,00"
                                    disabled="true"
                                />
                                <div
                                    v-if="form.errors.total"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.total }}
                                </div>
                            </div>
                            <div class="sm:col-span-4 space-x-2">
                                <Button
                                    variant="primary"
                                    type="submit"
                                    :disabled="formUpdate.processing"
                                    :class="{ 'opacity-25': formUpdate.processing }"
                                >
                                    SALVAR
                                </Button>   
                                <Button
                                    variant="default"
                                    type="button"
                                    @click="closeUpdate"
                                >
                                    CANCELAR
                                </Button>                       
                            </div>
                        </div>
                    </form>
                </div>  
            </div>
            <div v-if="success" class="text-center mt-10 h-screen align-middle">
                <h1 class="text-lg font-extralight">Conta à receber atualizada com sucesso</h1>
                <div class="mt-6 grid grid-cols-1 gap-x-6 px-5">
                    <div class="sm:col-span-4 space-x-2">
                        <Button
                            variant="primary"
                            type="button"
                            @click="closeUpdate"
                        >
                            FECHAR
                        </Button>                         
                    </div>
                </div> 
            </div>
        </Slideover>

        <NewReceivable :show="newTab" @closeSlide="closeSlide()"></NewReceivable>
    </AuthenticatedLayout>
</template>
