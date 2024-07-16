<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { throttle, pickBy } from 'lodash';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import Pagination from '@/Components/Pagination.vue';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
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

const breadcrumb = ref({ page:{id: 1, name: 'Proposta', url : "/proposta"}});

const props = defineProps({
    proposals: Object,
    users: Object,
    companies: Object,
    status: Object,
    origens: Object,    
    types: Object,    
    totals: Object,
    filters: Object,
    role: String,
});

const filter = reactive({
    term: props.filters.term.term,
    status: props.filters.status,
    origem: props.filters.origem,
    types: props.filters.types,
    date: props.filters.date,
    approved: props.filters.approved,
    user: props.filters.user,
    companies: props.filters.companies,
});

const filterTab = ref(false);

const form = useForm({
    id: null,
});

watch(filter,
    throttle( ()=>{
        let query = pickBy(filter);
        let queryRoute = route('proposal.index', Object.keys(query).length ? query : {
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

function approve(proposal) {
    if (confirm("Confirma a aprovação dessa proposta?")) {
        form.put(route('proposal.approve', proposal.id));
    }
}

function appropriation(proposal) {
    if (confirm("Confirma a apropriação dessa proposta?")) {
        form.put(route('proposal.appropriation', proposal.id));
    }
}

function clearFilter(){
    router.get('/proposta');
}

function openFilter(){
    filterTab.value = true;
}

function closeFilter(){
    filterTab.value = false;
}

function destroy(id) {
    if (confirm("Confirma a exclusão desta proposta?")) {
        form.delete(route('proposal.destroy', id));
    }
}

function unpprove(id) {
    if (confirm("Confirma o estorno da aprovação desta proposta?")) {
        form.put(route('proposal.unpprove', id));
    }
}

function newProposal(){
    alert('Para criar uma nova proposta você deve ir até a página de leads');
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
    .multiselect-tag{
        background: #38bdf8 !important; 
    }
</style>
<template>
    <Head title="Propostas" />  

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            
            <Alert v-if="$page.props.flash.message" :type="$page.props.flash.message['type']" @close="onClose()" class="mb-8">
                {{ $page.props.flash.message['msg'] }}
            </Alert>

            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-4 mb-10">
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL ABERTO</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[0].total > 0 ? vueNumberFormat(totals[0].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL APROVADO</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[1].total > 0 ? vueNumberFormat(totals[1].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL APROPRIADO</span>
                    <h1 class="text-sky-500 font-semibold text-1xl tracking-tighter sm:py-1 md:py-2">R$ {{ totals[2].total > 0 ? vueNumberFormat(totals[2].total, { prefix: '' }) : "0,00" }}</h1>
                </div>                
            </div>

            <div class="bg-white">
                <div
                    class="relative overflow-x-auto shadow-md sm:rounded-lg min-h-[450px]"
                >   
                    <div class="grid md:grid-cols-7 sm:grid-cols-4 gap-3 px-4 py-6 text-slate-600">
                        <div class="col-span-3">
                            <TextInput
                                    type="text"
                                    name="search"
                                    v-model="filter.term"
                                    class="w-full"
                                    placeholder="Pesquise por nome, grupo ou cota"
                                />
                        </div>
                        <div class="md:col-span-2 sm:col-span-8 bg-slate-200 sm:hidden md:block">
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
                                    Nome
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Produto
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Criada
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Aprovada
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Valor
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Status
                                </th>
                                <th v-if="props.role === 1 || props.role === 4 || props.role === 3" scope="col" class="px-4 font-light">
                                    Usuário
                                </th>
                                <th scope="col" class="px-6 w-64">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="proposal in proposals.data"
                                :key="proposal.id"
                                class="bg-white border-b border-slate-100  text-slate-600 even:bg-slate-50 odd:bg-white"
                            >
                                <td
                                    scope="row"
                                    class="px-4 py-4"
                                >
                                    <input type="checkbox" :id="`chk_${proposal.id}`" v-model="checked" />   
                                </td>
                                <td
                                    scope="row"
                                    class="px-4 w-full"
                                >
                                    {{ proposal.name }} - {{ proposal.group }}/{{ proposal.quota }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    {{ proposal.product }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    {{ moment(proposal.created_at).format("DD/MM/YYYY") }}
                                </td> 
                                <td v-if="proposal.approved_at != null"
                                    scope="row"
                                    class="px-4"
                                >
                                    {{ moment(proposal.approved_at).format("DD/MM/YYYY") }}
                                </td> 
                                <td v-else
                                    scope="row"
                                    class="px-4 text-center" 
                                >
                                    -
                                </td>
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    {{ vueNumberFormat(proposal.price) }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4"
                                >
                                    <span class="font-bold" :class="proposal.status_id === 4 ? 'text-red-500':proposal.status_id ===  11 ?'text-sky-500':'text-green-500'">
                                        {{ proposal.status_name }}
                                    </span>    
                                </td>
                                <td v-if="props.role === 1 || props.role === 4 || props.role === 3"
                                    scope="row"
                                    class="px-4 whitespace-nowrap"
                                >
                                    {{ proposal.usuario }}
                                </td>
                                <td class="flex space-x-2 py-3 px-4">
                                    <ButtonLink
                                        v-if="proposal.status_id === 4"
                                        variant="basic"
                                        :href="`/proposta/${proposal.id}/editar`"
                                        class=""
                                        title="Editar" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </ButtonLink>
                                    <ButtonLink
                                        v-if="proposal.status_id != 4"
                                        variant="basic"
                                        :href="`/proposta/${proposal.id}/editar`"
                                        class=""
                                        title="Visualizar" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                    </ButtonLink>
                                    <Button
                                        v-if="proposal.status_id === 4"    
                                        variant="basic"
                                        @click="approve(proposal)"
                                        class="" 
                                        title="Aprovar" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                                        </svg>
                                    </Button>
                                    <Button
                                        v-if="proposal.status_id === 11 && props.role === 1"    
                                        variant="basic"
                                        @click="appropriation(proposal)"
                                        class="" 
                                        title="Apropriar" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </Button>
                                    <Button
                                        v-if="proposal.status_id === 4 && props.role === 1"    
                                        variant="basic"
                                        @click="destroy(proposal)"
                                        class="" 
                                        title="Excluir" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </Button>
                                    <Button
                                        v-if="(proposal.status_id === 11 || proposal.status_id === 13) && props.role === 1"    
                                        variant="basic"
                                        @click="unpprove(proposal)"
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
                        <Pagination :links="proposals.links"></Pagination>
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
                                <div class="md:col-span-3 sm:col-span-8 sm:block md:hidden">
                                    <InputLabel class="text-xs">Propostas Criadas</InputLabel>
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
                                    <InputLabel class="text-xs">Propostas Aprovadas</InputLabel>
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
                            <div v-if="props.role === 1 || props.role === 3"  class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Empresas</InputLabel>
                                    <Multiselect
                                        v-model="filter.companies"
                                        :options="companies"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div v-if="props.role === 1 || props.role === 4 || props.role === 3"  class="grid grid-cols-3 gap-4">
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
                            <div v-if="props.role === 1 || props.role === 3"  class="grid grid-cols-3 gap-4">
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
                                    <InputLabel class="text-xs">Tipo de Proposta</InputLabel>
                                    <Multiselect
                                        v-model="filter.types"
                                        :options="types"
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
