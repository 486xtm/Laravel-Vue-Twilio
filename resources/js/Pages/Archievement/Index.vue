<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { throttle, pickBy } from 'lodash';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import Pagination from '@/Components/Pagination.vue';
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

const breadcrumb = ref({ page:{id: 1, name: 'Arquivamentos', url : "/arquivamento"}});

const props = defineProps({
    leads: Object,
    users: Object,
    status: Object,
    origens: Object,
    reasons: Object,
    filters: Object,
});

const filter = reactive({
    term: props.filters.term.term,
    status: props.filters.status,
    reasons: props.filters.reasons,
    origem: props.filters.origem,
    date: props.filters.date,
    user: props.filters.user,
});

const filterTab = ref(false);

const form = useForm({
    id: null,
});

watch(filter,
    throttle( ()=>{
        let query = pickBy(filter);
        let queryRoute = route('archievement.index', Object.keys(query).length ? query : {
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
    let queryRoute = route('archievement.export', Object.keys(query).length ? query : {
        remember: 'forget',
    });

    window.open(queryRoute);
}

function clearFilter(){
    router.get('/arquivamento');
}

function openFilter(){
    filterTab.value = true;
}

function closeFilter(){
    filterTab.value = false;
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
    .multiselect-tag{
        background: #38bdf8 !important; 
    }
</style>
<template>
    <Head title="Arquivamento" />  

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            
            <Alert v-if="$page.props.flash.message" type="info" @close="onClose()" class="mb-8">
                {{ $page.props.flash.message }}
            </Alert>

            <div class="bg-white">
                <div
                    class="relative overflow-x-auto shadow-md sm:rounded-lg min-h-[450px]"
                >   
                    <div class="grid md:grid-cols-7 sm:grid-cols-4 gap-3 px-4 py-6 text-slate-600 ">
                        <div class="col-span-3">
                            <TextInput
                                    type="text"
                                    name="search"
                                    v-model="filter.term"
                                    class="w-full"
                                    placeholder="Pesquise por nome"
                                />
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
                                <th scope="col" class="px-4 py-2.5 font-light">
                                    Lead
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Origem
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Canal
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Telefone
                                </th>                                                                                            
                                <th scope="col" class="px-4 font-light">
                                    Usuário
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="lead in leads.data"
                                :key="lead.id"
                                class="bg-white border-b border-slate-100  text-slate-600 even:bg-slate-50 odd:bg-white"
                            >
                                <td
                                    scope="row"
                                    class="px-4 py-4"
                                >
                                    {{ lead.name }} 
                                </td>        
                                <td
                                    scope="row"
                                    class="px-4 w-full"
                                >
                                    {{ lead.origem }} 
                                </td>        
                                <td
                                    scope="row"
                                    class="px-4 w-full"
                                >
                                    {{ lead.channel }} 
                                </td>                                
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    {{ lead.celular }}
                                </td>                                                                                                        
                                <td 
                                    scope="row"
                                    class="px-4 whitespace-nowrap"
                                >
                                    {{ lead.usuario }}
                                </td>                               
                            </tr>
                        </tbody>
                    </table>
                    <div class="w-full bg-white justify-end py-5">
                        <Pagination :links="leads.links"></Pagination>
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
                                    <InputLabel class="text-xs">Criação do Lead</InputLabel>
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
                                    <InputLabel class="text-xs">Razões</InputLabel>
                                    <Multiselect
                                        v-model="filter.reasons"
                                        :options="reasons"
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
                                              
                        </div>
                    </form>
                </div>  
            </div>
        </Slideover>
    </AuthenticatedLayout>
</template>
