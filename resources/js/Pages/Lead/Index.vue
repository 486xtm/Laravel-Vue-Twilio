<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch, reactive, computed, onMounted, onUnmounted  } from 'vue';
import { useRemember, Head, useForm } from '@inertiajs/vue3';
import { throttle, pickBy } from 'lodash';
import { router } from '@inertiajs/vue3';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import Pagination from '@/Components/Pagination.vue';
import { TailwindPagination } from 'laravel-vue-pagination';
import { Badge } from 'flowbite-vue';
import TextInput from '@/Components/TextInput.vue';
import Link from '@/Components/Link.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';
import Alert from '@/Components/Alert.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SlideOver from '@/Components/SlideOver.vue';
import Actions from './Partials/LeadActions.vue';
import Proposal from './Partials/Proposal.vue';
import Forward from './Partials/Forward.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Multiselect from '@vueform/multiselect';  
import VueDatePicker from '@vuepic/vue-datepicker';
import moment from "moment";
import 'moment/locale/pt-br';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import '@vuepic/vue-datepicker/dist/main.css'

const breadcrumb = ref({ page:{id: 1, name: 'Leads', url : "/lead"}});
const selectedTab = ref(0)
const filterTab = ref(false);
const proposalShow = ref(false);
const forwardShow = ref(false);
const leadProposal = ref([{}]);
const star = ref(5);

const props = defineProps({
    leads: Object,
    schedules: Object,
    products: Object,
    origens: Object,
    channels: Object,
    status: Object,
    filters: Object,
    funnelSteps: Object,
    proposalTypes: Object,
    users: Object,
    companies: Object,
    kabanCards: Object,
    role: String,
});

const leadFilter = reactive({
    term: props.filters.term.term,
    product: props.filters.product,
    origem: props.filters.origem,
    channel: props.filters.channel,
    status: props.filters.status,
    funnelStep: props.filters.funnelStep, 
    date: props.filters.date,
    companies: props.filters.companies,
    user: props.filters.user,
    bots: props.filters.bots,
    stars: props.filters.stars,
});

const allLeads = ref([]);
const allLeadsCount = ref(0);
const leadsWaiting = ref([]);
const leadsWaitingCount = ref(0);
const leadsSchedule = ref([]);
const leadsScheduleCount = ref(0);
const leadsFavorite = ref([]);
const leadsFavoriteCount = ref(0);
const leadsPendding = ref([]);
const leadsPenddingCount = ref(0);
const botStatus = ref([
    { value: 1,
      label: 'Sem Interação',      
    },
    { value: 2,
      label: 'Interagiu',      
    },
    { value: 4,
      label: 'Concluiu',      
    },
]);

watch(leadFilter,
    throttle( ()=>{    
        let query = pickBy(leadFilter);
        let queryRoute = route('lead.index', Object.keys(query).length ? query : {
            remember: 'forget',
        });
        
        router.get(
            queryRoute, 
            {},
            {
            preserveState:true,
            replace:true 
        });
        getLeads();
        getWaiting();
        getSchedules();
        getPendding();
        getFavorite();
    }, 500),
    {
        deep: true,
    }
);

async function getLeads(page = 1) {
  try {
    const response = await axios.get('/leads', {
        params: {
          term: leadFilter.term,
          product: leadFilter.product,
          origem: leadFilter.origem,
          channel: leadFilter.channel,
          status: leadFilter.status,
          funnelStep: leadFilter.funnelStep,
          date: leadFilter.date,
          user: leadFilter.user,
          companies: leadFilter.companies,
          stars: leadFilter.stars,
          bots: leadFilter.bots,
          page: page
        }
    });
        
    allLeads.value = response.data;
    allLeadsCount.value = allLeads.value.total;
  } catch (error) {
    // Handle errors
    console.error('Error:', error);
  }
}

async function getWaiting(page = 1) {
  try {
    const response = await axios.get('/leadswaiting', {
        params: {
          term: leadFilter.term,
          product: leadFilter.product,
          origem: leadFilter.origem,
          channel: leadFilter.channel,
          status: leadFilter.status,
          funnelStep: leadFilter.funnelStep,
          date: null,
          user: leadFilter.user,
          companies: leadFilter.companies,
          stars: leadFilter.stars,
          bots: leadFilter.bots,
          page: page
        }
    });
        
    leadsWaiting.value = response.data;
    leadsWaitingCount.value = leadsWaiting.value.total;
  } catch (error) {
    // Handle errors
    console.error('Error:', error);
  }
}

async function getSchedules(page = 1){
    const response = await axios.get('/leadsschedule', {
        params: {
          term: leadFilter.term,
          product: leadFilter.product,
          origem: leadFilter.origem,
          channel: leadFilter.channel,
          status: leadFilter.status,
          funnelStep: leadFilter.funnelStep,
          date: null,
          user: leadFilter.user,
          companies: leadFilter.companies,
          stars: leadFilter.stars,
          bots: leadFilter.bots,
          page: page 
        }
    });
    
    leadsSchedule.value = response.data;
    leadsScheduleCount.value = leadsSchedule.value.total;
}

async function getFavorite(page = 1){
    const response = await axios.get('/leadsfavorite', {
        params: {
          term: leadFilter.term,
          product: leadFilter.product,
          origem: leadFilter.origem,
          channel: leadFilter.channel,
          status: leadFilter.status,
          funnelStep: leadFilter.funnelStep,
          date: leadFilter.date,
          user: leadFilter.user,
          companies: leadFilter.companies,
          stars: leadFilter.stars,
          bots: leadFilter.bots,
          page: page 
        }
    });
    
    leadsFavorite.value = response.data;
    leadsFavoriteCount.value = leadsFavorite.value.total;
}

async function getPendding(page = 1){
    const response = await axios.get('/leadspendding', {
        params: {
          term: leadFilter.term,
          product: leadFilter.product,
          origem: leadFilter.origem,
          channel: leadFilter.channel,
          status: leadFilter.status,
          funnelStep: leadFilter.funnelStep,
          date: leadFilter.date,
          user: leadFilter.user,
          companies: leadFilter.companies,
          stars: leadFilter.stars,
          bots: leadFilter.bots,
          page: page 
        }
    });
    
    leadsPendding.value = response.data;
    leadsPenddingCount.value = leadsPendding.value.total;
}

function clearFilter(){
    router.get('/lead');
}

function exportLeads(){
    let query = pickBy(leadFilter);
    let queryRoute = route('lead.export', Object.keys(query).length ? query : {
        remember: 'forget',
    });

    window.open(queryRoute);
}

function openFilter(){
    filterTab.value = true;
}

function closeFilter(){
    filterTab.value = false;
}

function openProposal(lead){
    leadProposal.value = lead;
    proposalShow.value = true;
}

function forwardLeads(lead){
    forwardShow.value = true;
}

function closeForward(lead){
    forwardShow.value = false;
}

function closeSlide(){
    proposalShow.value = false;
}

function countDays(p){
    var currentDate = moment(new Date()).format("YYYY-MM-DD HH:mm:ss");
    var date = new Date(p); 
    var time = moment(currentDate).diff(date, 'hour');
    
    if(time === 0)
        time = moment(currentDate).diff(date, 'minutes')+" min";
    else if(time <= 48)
        time = moment(currentDate).diff(date, 'hour')+" horas";
    else
        time = moment(currentDate).diff(date, 'day')+" dias";

    return time;
}

function changeTab(index) {
  selectedTab.value = index
}

function reload() {
    getWaiting();
    getSchedules();    
    getPendding();
    getLeads();
    getFavorite();
}

onMounted(() => {
    getWaiting();
    getSchedules();    
    getPendding();
    getLeads();
    getFavorite();

    /*setInterval(() => {
        getWaiting();
    }, 60000);  */

    
});





</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
    .multiselect-tag{
        background: #38bdf8 !important; 
    }

    
    html {
        scroll-behavior: smooth;
    }
</style>
<template>
    <Head title="Leads" />

    <AuthenticatedLayout>
        
        <div class="max-w-7xl mx-auto md:px-10 sm:px-4 lg:px-10 mb-5 sm:pb-12">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-1 sm:block md:hidden"/>

            <div class="fixed bottom-28 right-2 text-blue-500 sm:block md:hidden z-50 ">
                <Link
                    class="px-3 py-3.5 w-10 h-10 bg-sky-500 rounded-full hover:bg-sky-700 active:shadow-lg mouse shadow transition ease-in duration-200 focus:outline-none"
                    value="/lead/criar">
                    <svg viewBox="0 0 20 20" enable-background="new 0 0 20 20" class="w-5 h-5 inline-block">
                        <path fill="#FFFFFF" d="M16,10c0,0.553-0.048,1-0.601,1H11v4.399C11,15.951,10.553,16,10,16c-0.553,0-1-0.049-1-0.601V11H4.601
                                            C4.049,11,4,10.553,4,10c0-0.553,0.049-1,0.601-1H9V4.601C9,4.048,9.447,4,10,4c0.553,0,1,0.048,1,0.601V9h4.399
                                            C15.952,9,16,9.447,16,10z" />
                    </svg>
                </Link>
            </div>
            <div class="pt-10">
                <Alert v-if="$page.props.flash.message" type="info" @close="onClose()">
                    {{ $page.props.flash.message }}
                </Alert>
            </div>
            
             <!-- SEARCH -->
             <div class="grid sm:grid-cols-12 md:grid-cols-10 md:gap-2 sm:gap-1 md:mt-10 sm:mt-0 mb-5 text-slate-600 ">
                <div class="col-span-1 md:block sm:hidden">
                    <ButtonLink 
                        variant="primary"
                        class="block h-9"
                        value="/lead/criar">NOVO LEAD 
                    </ButtonLink>
                </div>
                <div class="md:col-span-3 sm:col-span-8 bg-slate-200">
                    <TextInput
                        type="text"
                        v-model="leadFilter.term"
                        placeholder="Pesquise por nome, email ou telefone"
                        class="h-9 sm:w-full"
                    />
                </div>
                <div class="md:col-span-3 sm:col-span-8 bg-slate-200 sm:hidden md:block">
                    <VueDatePicker 
                        v-model="leadFilter.date" 
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
                <div class="flex justify-between w-full">
                    <div class="flex">
                        <Button 
                            variant="default" 
                            type="button" 
                            class="ml-2 py-2.5 md:flex"
                            title="Filtro Avançado"
                            @click="openFilter">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-sky-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                        </Button>
                        <Button 
                            variant="default" 
                            type="button" 
                            class="ml-2 py-2.5 md:flex "
                            title="Limpar Filtro"
                            @click="clearFilter">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </Button>
                    </div>
                    <div class="sm:hidden md:block" v-if="props.role === 1">
                        <Button 
                            variant="default" 
                            type="button" 
                            class="ml-2 h-9 py-0.5 md:flex"
                            title="Encaminhar Leads"
                            @click="forwardLeads">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
                            </svg>
                        </Button>
                    </div>
                    <div class="sm:hidden md:block" v-if="props.role === 1">
                        <Button 
                            variant="default" 
                            type="button" 
                            class="ml-2 h-9 py-0.5 md:flex"
                            title="Exportar para Excel"
                            @click="exportLeads">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                            </svg>
                        </Button>
                    </div>
                </div>
            </div>
            <TabGroup :selectedIndex="selectedTab" @change="changeTab">
                <TabList class="pt-2 border-b text-sm border-slate-200 rounded-sm sm:hidden md:block">
                    <!-- Use the `selected` state to conditionally style the selected tab. -->
                    <Tab as="template" v-slot="{ selected }">
                        <button
                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                        class="px-6 py-2"
                        >
                        A Fazer ({{leadsWaitingCount + leadsScheduleCount + leadsPenddingCount  }})
                        </button>
                    </Tab>
                    <Tab as="template" v-slot="{ selected }">
                        <button
                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                        class="px-6 py-2"
                        @click="getFavorite"
                        >
                        Favoritos ({{ leadsFavoriteCount }})
                        </button>
                    </Tab>
                    <Tab as="template" v-slot="{ selected }">
                        <button
                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                        class="px-6 py-2"
                        @click="getLeads"
                        >
                        Todos ({{allLeadsCount}})
                        </button>
                    </Tab>
                    <!-- ... -->
                </TabList>
                <TabPanels>
                    <TabPanel>
                        <!--  AGUARDANDO ATENDIMENTO  -->
                        <div class="flex justify-end space-x-2 sm:pb-4">
                            <a href="#leadsSchedule" class="bg-sky-500 hover:bg-sky-300 text-white text-xs px-2 py-1 rounded-md mt-2 shadow-sm" v-if="leadsScheduleCount > 0">Agendamentos <span class="text-sky-500 bg-white px-1 py-0.5 h-4 rounded-md ml-1 text-xs">{{ leadsScheduleCount }}</span></a>
                            <a href="#leadsPendding" class="bg-amber-500 hover:bg-amber-300 text-white text-xs px-2 py-1 rounded-md mt-2 shadow-sm" v-if="leadsPenddingCount > 0">Pendentes <span class="text-amber-500 bg-white px-1 py-0.5 h-4 rounded-md ml-1 text-xs">{{ leadsPenddingCount }}</span></a>
                        </div>
                        <div 
                             v-if="leadsWaitingCount > 0" class="flex justify-center pb-4 font-extralight text-sm text-slate-400 tracking-wider">
                             <h1>AGUARDANDO ATENDIMENTO ({{leadsWaitingCount}})</h1>                            
                        </div>
                        <div v-if="(leadsWaitingCount + leadsScheduleCount + leadsPenddingCount) === 0" class="flex text-sm mt-4">
                            <span class="font-extralight text-sm tracking-wider text-slate-600 text-center w-full uppercase">sua lista está vazia</span>
                        </div>
                        
                        <ul>
                            <li
                                v-for="lead in leadsWaiting.data"
                                :key="lead.id"    
                                class="bg-white px-5 py-5 mb-5 shadow-md">
                                <div class="flex justify-between border-l-4 border-red-600 pl-4">
                                    <div class="mb-3">
                                        <Link :href="`/lead/${lead.id}/`" class="font-bold text-sky-900">{{ lead.name }}</Link>
                                        <p class="sm:text-xxs md:text-xs text-gray-500">{{ lead.product }}</p>                                        
                                    </div>
                                    <div class="flex space-x-1 h-10 z-10">
                                        <Actions :lead="lead" @reloadLeads="reload();"></Actions>
                                    </div>
                                </div>
                                <div class="flex mt-4">
                                    <Badge class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md sm:text-xxs md:text-xs font-light">{{ lead.channel }}</Badge>                               
                                    <Badge class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md sm:text-xxs md:text-xs font-light">{{ lead.origem }}</Badge>                                    
                                </div>
                                <div class="justify-between md:flex mt-4">                                
                                    <div class="flex text-slate-400 justify-between">
                                        <span class="text-xs text-red-600 font-semibold mr-2">{{ lead.status}}</span>
                                        <div v-if="lead.forward === 1" title="Encaminhado" class="text-slate-800 pr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
                                            </svg>
                                        </div>                                        
                                        <span class="text-xxs cursor-default pt-0.5" title="Usuário Responsável">Aguardando há {{ countDays(lead.created_at)}} </span>                                        
                                    </div>
                                    <div class="flex text-slate-400 space-x-2 md:pt-2 sm:pt-1 justify-between">
                                        <span class="text-slate-400 sm:text-xxs md:text-xxs pt-0.5" >Recebido em {{ moment(lead.created_at).format("DD/MM/YYYY HH:mm")}}</span>
                                        <span v-if="props.role === 1 || props.role === 4 ||  props.role === 3" class=" pt-0.5 text-xxs cursor-default" title="Usuário Responsável">{{ lead.usuario}}</span>
                                        <div v-if="lead.bot_stage >= 1" :title="`Interagiu com o Bot (${lead.bot_stage})`">
                                            <span :class="`${lead.bot_stage === 4 ? 'text-green-400': lead.bot_stage === 1 ?  'text-slate-400':lead.bot_stage > 1 ?  'text-amber-400':''}`" class="text-xs mr-2"><font-awesome-icon class="w-5 h-5" icon="fa-solid fa-robot" /></span>                                            
                                        </div>
                                    </div>                              
                                </div>
                            </li>
                        </ul>
                        <div class="flex justify-end">
                            <TailwindPagination
                                :data="leadsWaiting"
                                :limit="1"
                                :keepLength="true"
                                @pagination-change-page="getWaiting"
                            />
                        </div>
                        <!--  ATIVIDADES PARA HOJE  -->

                        <div id="leadsSchedule" v-if="leadsScheduleCount > 0" class="flex justify-center pt-4 font-extralight text-sm text-slate-400 tracking-wider">
                            <h1>ATIVIDADES PARA HOJE ({{ leadsScheduleCount }})</h1>
                        </div>
                        <ul class="pt-4">
                            <li 
                                v-for="lead in leadsSchedule.data"
                                :key="lead.id"    
                                class="bg-white px-4 py-4 mb-5 shadow-md">
                                <div class="flex justify-between border-l-4 border-sky-500 pl-4 mb-4">
                                    <div class="mb-3">
                                        <div class="flex space-x-2 mb-2">
                                            <div class="bg-slate-100 h-1 w-6" v-for="item in funnelSteps" :key="item.id">
                                                <div v-if="item.value === lead.funnel_step_id" :title="item.label" class="h-1 w-6" :style="`background-color: ${item.color};`"></div>
                                                <div v-else :title="item.label" class="h-1 w-6"></div>
                                            </div>                                                                                        
                                        </div>
                                        <Link :href="`/lead/${lead.id}/`" class="font-bold text-sky-900">{{ lead.name }}</Link>
                                        <p class="text-xs text-gray-500">{{ lead.product }}</p>                                        
                                    </div>
                                    <div class="flex space-x-1 h-10 z-10">
                                        <Actions :lead="lead" @openProposal="openProposal(lead)" @reloadLeads="reload();"></Actions>
                                    </div>
                                </div>                                
                                <div class="flex text-sky-500 font-semibold space-x-1" title="Retornar para o cliente">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Hoje {{ moment(lead.schedule_date).format("HH:mm") }}</span>
                                </div>
                                <div class="flex mt-2 text-slate-900">
                                    <Badge title="Origem" class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md sm:text-xxs md:text-xs font-light">{{ lead.channel }}</Badge>
                                    <Badge title="Canal" class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md sm:text-xxs md:text-xs font-light">{{ lead.origem }}</Badge>
                                </div>
                                <div class="justify-between md:flex mt-4">   
                                    <div class="flex">
                                        <div class="flex">
                                            <span class="text-sky-500 text-xs font-semibold mr-2">{{ lead.status}}</span>
                                        </div>   
                                        <div v-if="lead.forward === 1" title="Encaminhado" class="text-slate-800 pr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
                                            </svg>
                                        </div>
                                    </div>                          
                                    <div class="flex text-slate-400 space-x-2 md:pt-2 sm:pt-1 justify-between">
                                        <span class="text-slate-400 text-xxs pt-0.5">Recebido em {{ moment(lead.created_at).format("DD/MM/YYYY HH:mm") }}</span>
                                        <span v-if="props.role === 1 || props.role === 4 ||  props.role === 3" class="pt-0.5 text-slate-400 text-xxs cursor-default">{{ lead.usuario}}</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="flex justify-end">
                            <TailwindPagination
                                :data="leadsSchedule"
                                :limit="1"
                                :keep-length="true"
                                @pagination-change-page="getSchedules"
                            />
                        </div>
                        <!--  PENDENTES  -->
                        <div id="leadsPendding" v-if="leadsPenddingCount > 0" class="flex justify-center sm:py-2 md:py-3 font-extralight text-sm text-slate-400 tracking-wider"><h1>PENDENTES ({{ leadsPenddingCount }})</h1></div>
                        <ul class="pt-2">
                            <li 
                                v-for="lead in leadsPendding.data"
                                :key="lead.id"    
                                class="bg-white px-5 py-5 sm:mb-3 md:mb-5 shadow-md">
                                <div class="flex justify-between border-l-4 border-amber-500 pl-4 mb-4">
                                    <div class="mb-3">
                                        <div class="flex space-x-2 mb-2">
                                            <div class="bg-slate-100 h-1 w-6" v-for="item in funnelSteps" :key="item.id">
                                                <div v-if="item.value === lead.funnel_step_id" :title="item.label" class="h-1 w-6" :style="`background-color: ${item.color};`"></div>
                                                <div v-else :title="item.label" class="h-1 w-6"></div>
                                            </div>                                                                                        
                                        </div>
                                        <Link :href="`/lead/${lead.id}/`" class="font-bold text-sky-900">{{ lead.name }}</Link>
                                        <p class="text-xs text-gray-500">{{ lead.product }}</p>                                       
                                    </div>
                                    <div class="flex space-x-1 h-10 z-10">
                                        <Actions :lead="lead"  @openProposal="openProposal(lead)" @reloadLeads="reload();"></Actions>
                                    </div>
                                </div> 
                                <div class="flex text-amber-500 font-semibold space-x-1" title="Retornar para o cliente">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                    </svg>
                                    <span>{{ moment(lead.schedule_date).format("DD/MM HH:mm") }}</span>
                                </div>
                                <div class="flex mt-4 ">
                                    <Badge title="Origem" class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md font-light sm:text-xxs md:text-xs">{{ lead.channel }}</Badge>
                                    <Badge title="Canal" class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md font-light sm:text-xxs md:text-xs">{{ lead.origem }}</Badge>
                                </div>
                                <div class="justify-between md:flex mt-4">                                
                                    <div class="flex text-slate-400 justify-between">
                                        <div class="flex">
                                            <div class="flex">
                                                <span class="text-amber-500 text-xs font-semibold mr-2">{{ lead.status}} </span>
                                            </div>   
                                            <div v-if="lead.forward === 1" title="Encaminhado" class="text-slate-800 pr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
                                                </svg>
                                            </div>
                                        </div> 
                                        <span v-if="lead.schedule_date && lead.status_id === 7" class="text-xxs pt-0.5"> Retornar em {{ moment(lead.schedule_date).format("DD/MM/YYYY HH:mm") }}</span>
                                        <span v-if="lead.schedule_date === null && lead.status_id === 7" class="text-xxs pt-0.5"> Sem agendamentos</span>
                                        <span v-if="lead.status_id === 10" class="text-xxs pt-0.5"> Última Interação {{ moment(lead.interaction_date).format("DD/MM/YYYY HH:mm") }}</span>
                                    </div>
                                    <div class="flex text-slate-400 space-x-2 md:pt-2 sm:pt-1 justify-between">
                                        <span class="text-slate-400 text-xxs pt-0.5"> Recebido em {{ moment(lead.created_at).format("DD/MM/YYYY HH:mm") }}</span> 
                                        <span v-if="props.role === 1 || props.role === 4 ||  props.role === 3" class="text-xxs pt-0.5">{{ lead.usuario}}</span>
                                    </div>                              
                                </div>

                            </li>
                        </ul>
                        <div class="flex justify-end">
                            <TailwindPagination
                                :data="leadsPendding"
                                :limit="1"
                                :keepLength="true"
                                @pagination-change-page="getPendding"
                            />
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <ul class="mt-6">
                            <li 
                                v-for="lead in leadsFavorite.data"
                                :key="lead.id"    
                                class="bg-white px-5 py-5 mb-5 shadow-md">
                                <div :class="`flex justify-between border-l-4 ${(lead.status_id === 7 && lead.schedule_date === null)? 'border-red-500' : (lead.status_id === 7 && lead.schedule_date != null) ? 'border-sky-500': lead.status_id === 9 ? 'border-red-600':  lead.status_id === 8 ? 'border-slate-500': lead.status_id === 10 ? 'border-amber-500': lead.status_id === 16 ? 'border-green-500': ''} pl-4 mb-4`">
                                    <div class="mb-3">
                                        <div class="flex space-x-2 mb-2">
                                            <div class="bg-slate-100 h-1 w-6" v-for="item in funnelSteps" :key="item.id">
                                                <div v-if="item.value === lead.funnel_step_id" :title="item.label" class="h-1 w-6" :style="`background-color: ${item.color};`"></div>
                                                <div v-else :title="item.label" class="h-1 w-6"></div>
                                            </div>                                                                                        
                                        </div>
                                        <Link :href="`/lead/${lead.id}/`" class="font-bold text-sky-900">{{ lead.name }}</Link>
                                        <p class="text-xs text-gray-500">{{ lead.product }}</p>                                        
                                    </div>
                                    <div class="flex space-x-1 h-10 z-10">
                                        <Actions :lead="lead" @openProposal="openProposal(lead)" @reloadLeads="reload();"></Actions>
                                    </div>
                                </div>
                                <div class="flex mt-4">
                                    <Badge class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md text-xxs font-light">{{ lead.channel }}</Badge>
                                    <Badge class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md text-xxs font-light">{{ lead.origem }}</Badge>
                                </div>
                                <div class="justify-between md:flex mt-4">                                
                                    <div class="flex text-slate-400 justify-between">
                                        <span class="text-xs" :class="(lead.status_id === 7 && lead.schedule_date === null)? 'text-red-600 font-semibold mr-2' : (lead.status_id === 7 && lead.schedule_date != null) ? 'text-sky-500 font-semibold mr-2': lead.status_id === 9 ? 'text-red-600 font-semibold mr-2':  lead.status_id === 8 ? 'text-slate-500 font-semibold mr-2': lead.status_id === 10 ? 'text-amber-500 font-semibold mr-2': lead.status_id === 16 ? 'text-green-500 font-semibold mr-2': '' ">{{ lead.status}}</span>
                                        <span v-if="lead.schedule_date && lead.status_id === 7" class="text-xxs pt-0.5"> Retornar em {{ moment(lead.schedule_date).format("DD/MM/YYYY HH:mm") }}</span>
                                        <span v-if="lead.schedule_date === null && lead.status_id === 7" class="text-xxs pt-0.5"> Sem agendamentos</span>
                                        <span v-if="lead.status_id === 8" class="text-xxs pt-0.5"> Última Interação {{ moment(lead.interaction_date).format("DD/MM/YYYY HH:mm") }}</span>
                                        
                                    </div>
                                    <div class="flex text-slate-400 space-x-2 md:pt-2 sm:pt-1 justify-between">
                                        <span class="text-slate-400 text-xxs pt-0.5">Recebido em {{ moment(lead.created_at).format("DD/MM/YYYY HH:mm") }}</span>
                                        <span v-if="props.role === 1 || props.role === 4 ||  props.role === 3" class="text-xxs pt-0.5">{{ lead.usuario}}</span>
                                    </div>                              
                                </div>
                            </li>
                        </ul>
                        <div class="flex justify-end">
                            <TailwindPagination
                                :data="leadsFavorite"
                                :limit="1"
                                :keepLength="true"
                                @pagination-change-page="getFavorite"
                            />
                        </div>
                        <div v-if="leadsFavoriteCount === 0" class="flex text-sm">
                            <span class="font-extralight text-sm tracking-wider text-slate-600 text-center w-full uppercase">sua lista está vazia</span>
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <ul class="mt-6">
                            <li 
                                v-for="lead in allLeads.data"
                                :key="lead.id"    
                                class="bg-white px-5 py-5 mb-5 shadow-md">

                                <div :class="`flex justify-between border-l-4 ${(lead.status_id === 7 && lead.schedule_date === null)? 'border-red-500' : (lead.status_id === 7 && lead.schedule_date != null) ? 'border-sky-500': lead.status_id === 9 ? 'border-red-600':  lead.status_id === 8 ? 'border-slate-500': lead.status_id === 10 ? 'border-amber-500': lead.status_id === 16 ? 'border-green-500': ''} pl-4 mb-4`">
                                    <div class="mb-3">
                                        <div class="flex space-x-2 mb-2">
                                            <div class="bg-slate-100 h-1 w-6" v-for="item in funnelSteps" :key="item.id">
                                                <div v-if="item.value === lead.funnel_step_id" :title="item.label" class="h-1 w-6" :style="`background-color: ${item.color};`"></div>
                                                <div v-else :title="item.label" class="h-1 w-6"></div>
                                            </div>                                                                                        
                                        </div>
                                        <Link :href="`/lead/${lead.id}/`" class="font-bold text-sky-900">{{ lead.name }}</Link>
                                        <p class="text-xs text-gray-500">{{ lead.product }}</p>                                        
                                    </div>
                                    <div class="flex space-x-1 h-10 z-10">
                                        <Actions :lead="lead" @openProposal="openProposal(lead)" @reloadLeads="reload();"></Actions>
                                    </div>
                                </div>                            
                                <div class="flex mt-4">                                    
                                    <Badge class="bg-slate-50 text-sky-900 px-1 py-1 rounded-none text-xxs font-light">{{ lead.channel }}</Badge>
                                    <Badge class="bg-slate-50 text-sky-900 px-1 py-1 rounded-none text-xxs font-light">{{ lead.origem }}</Badge>
                                </div>
                                
                                <div class="justify-between md:flex mt-4">                                
                                    <div class="flex text-slate-400 justify-between">
                                        <span class="text-xs" :class="(lead.status_id === 7 && lead.schedule_date === null)? 'text-red-600 font-semibold mr-2' : (lead.status_id === 7 && lead.schedule_date != null) ? 'text-sky-500 font-semibold mr-2': lead.status_id === 9 ? 'text-red-600 font-semibold mr-2':  lead.status_id === 8 ? 'text-slate-500 font-semibold mr-2': lead.status_id === 10 ? 'text-amber-500 font-semibold mr-2': lead.status_id === 16 ? 'text-green-500 font-semibold mr-2': '' ">{{ lead.status}}</span>
                                        <span v-if="lead.schedule_date && lead.status_id === 7" class="text-xxs pt-0.5"> Retornar em {{ moment(lead.schedule_date).format("DD/MM/YYYY HH:mm") }}</span>
                                        <span v-if="lead.schedule_date === null && lead.status_id === 7" class="text-xxs pt-0.5"> Sem agendamentos</span>
                                        <span v-if="lead.status_id === 8" class="text-xxs pt-0.5"> Última Interação {{ moment(lead.interaction_date).format("DD/MM/YYYY HH:mm") }}</span>
                                        
                                    </div>
                                    <div class="flex text-slate-400 space-x-2 md:pt-2 sm:pt-1 justify-between">
                                        <span class="text-slate-400 text-xxs pt-0.5">Recebido em {{ moment(lead.created_at).format("DD/MM/YYYY HH:mm") }}</span>
                                        <span v-if="props.role === 1 || props.role === 4 ||  props.role === 3" class="text-xxs pt-0.5">{{ lead.usuario}}</span>
                                    </div>                              
                                </div>
                            </li>
                        </ul>
                        <div class="flex justify-end">
                            <TailwindPagination
                                :data="allLeads"
                                :limit="1"
                                :keepLength="true"
                                @pagination-change-page="getLeads"
                            />
                        </div>
                        <div v-if="allLeadsCount === 0" class="flex text-sm">
                            <span class="font-extralight text-sm tracking-wider text-slate-600 text-center w-full uppercase">sua lista está vazia</span>
                        </div>
                    </TabPanel>
                <!-- ... -->
                </TabPanels>
            </TabGroup>
        </div><br><br><br>

        <!-- FILTER -->
        <SlideOver :showSlide="filterTab" title="Filtro Avançado" @close="closeFilter()">
            <div>
                <div class="bg-white">
                    <form @submit.prevent="filterLeads">
                        <div class="p-5 py-5 space-y-5">
                            <div class="grid grid-cols-3 gap-4 sm:block md:hidden">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Período</InputLabel>
                                    <VueDatePicker 
                                        v-model="leadFilter.date" 
                                        format="dd/MM/yyyy" 
                                        locale="pt-br"
                                        cancelText="cancelar" 
                                        selectText="selecione" 
                                        :enable-time-picker="false"
                                        range 
                                        utc>
                                    </VueDatePicker>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Produto</InputLabel>
                                    <Multiselect
                                        v-model="leadFilter.product"
                                        :options="products"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Etapa de Funil</InputLabel>
                                    <Multiselect
                                        v-model="leadFilter.funnelStep"
                                        :options="funnelSteps"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Status</InputLabel>
                                    <Multiselect
                                        v-model="leadFilter.status"
                                        :options="status"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Origem</InputLabel>
                                    <Multiselect
                                        v-model="leadFilter.origem"
                                        :options="origens"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Canal</InputLabel>
                                    <Multiselect
                                        v-model="leadFilter.channel"
                                        :options="channels"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div v-if="props.role === 1 || props.role === 3"  class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Empresas</InputLabel>
                                    <Multiselect
                                        v-model="leadFilter.companies"
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
                                        v-model="leadFilter.user"
                                        :options="users"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Bot</InputLabel>
                                    <Multiselect
                                        v-model="leadFilter.bots"
                                        :options="botStatus"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                                </div>
                            </div>
                            <div>
                                <InputLabel class="text-xs">Classificação</InputLabel>
                                <!-- 5 STARS -->
                                <div class="flex space-x-2 mt-2">
                                    <div class=""
                                        v-for="index in star"
                                        :key="index"
                                    >
                                        <input 
                                            type="radio" 
                                            name="stars" 
                                            :value="index" 
                                            v-model="leadFilter.stars" 
                                        />
                                        <label class="text-xs pl-1">{{ index }} </label>
                                    </div>
                                </div>
                            </div>
                            <div v-if="props.role === 1" class="sm:pb-16">
                                <Button 
                                    variant="default" 
                                    type="button" 
                                    class="py-2.5 md:flex sm:flex"
                                    title="Exportar para Excel"
                                    @click="exportLeads">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                                    </svg> <span class="ml-1 mt-0.5">Exportar</span> 
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>  
            </div>
        </Slideover>

        <Proposal :show="proposalShow" page="/lead" :types="proposalTypes" :products="products" :lead="leadProposal" @closeSlide="closeSlide()" @closeProposal="closeSlide()" ></Proposal>
        <Forward :show="forwardShow" page="/lead" :companies="companies" :status="status" :users="users" @closeSlide="closeForward()" @closeForward="closeForward()" ></Forward>


        <div class="fixed bottom-0 left-0 z-40 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600 sm:block md:hidden">
            <div class="grid h-full max-w-lg grid-cols-3 mx-auto font-medium">
                <button type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group" @click="changeTab(0)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    </svg>
                    <span class="text-xxs text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                        A Fazer ({{leadsWaitingCount + leadsScheduleCount + leadsPenddingCount }})
                    </span>
                </button>
                <button type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group" @click="changeTab(1)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>

                    <span class="text-xxs text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                        Favoritos ({{ leadsFavoriteCount}})
                    </span>
                </button>
                <button type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group" @click="changeTab(2)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z" />
                    </svg>

                    <span class="text-xxs text-gray-500 dark:text-gray-400 group-hover:text-sky-600 dark:group-hover:text-sky-500"> 
                        Todos ({{allLeadsCount}})
                    </span>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
