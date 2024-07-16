<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {ref, watch, computed, onMounted,reactive } from 'vue';
import { throttle, pickBy } from 'lodash';
import { router } from '@inertiajs/vue3';
import moment from "moment";
import { Head } from '@inertiajs/vue3';
import Badge from '@/Components/Badge.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SlideOver from '@/Components/SlideOver.vue';
import ChartBar from '@/Components/ChartBar.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import Multiselect from '@vueform/multiselect';  
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    dashboard: Object,
    filters: Object,
    companies: Object,
    users: Object,
    role: String,
});

const currentDate = moment(new Date()).format("YYYY-MM-DD");
const endDate = new Date(currentDate +" 23:59:50");
const startDate = new Date(currentDate +" 00:00:00");
const filterTab = ref(false);
const role_id = 0;

const filter = reactive({
    date: props.filters.date,
    companies: props.filters.companies,
    users: props.filters.users,
});

watch(filter,
    throttle( ()=>{
        let query = pickBy(filter);
        let queryRoute = route('dashboard', Object.keys(query).length ? query : {
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

function today(p){
    if(new Date(p) > startDate && new Date(p) < endDate){
        return true;
    }else{
        return false;
    }
}

function openFilter(){
    filterTab.value = true;
}

function closeFilter(){
    filterTab.value = false;
}

const userRanking = computed(() => {
    var data = "";
    let ranking = props.dashboard[0]['ranking'];

    data = ranking.slice().sort((a, b) => (parseFloat(a.dealsOpenTotal) > parseFloat(b.dealsOpenTotal) ? -1 : 1));
    data = data.slice().sort((a, b) => (parseFloat(a.dealsTotal) > parseFloat(b.dealsTotal) ? -1 : 1));
    return data;   
});

const waitingRanking = computed(() => {
    var data = "";
    let ranking = props.dashboard[0]['waiting'];
    data = ranking.slice().sort((a, b) => (a.total > b.total ? -1 : 1));
    return data;   
});

const formRanking = computed(() => {
    var data = "";
    let ranking = props.dashboard[0]['forms'];
    data = ranking.slice().sort((a, b) => (a.views > b.views ? -1 : 1));
    data = data.slice().sort((a, b) => (a.deals > b.deals ? -1 : 1));
    return data;   
});


onMounted(() => {
    console.log(props);
});

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
    .multiselect-tag{
        background: #38bdf8 !important; 
    }
</style>
<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>
        
        <template  v-for="data in dashboard" 
            :key="data.id">
            <div class="py-1">
                <div class="max-w-7xl mx-auto sm:px-4 mt-4 space-y-6 md:px-10">
                    <div class="flex justify-end">
                        <div>
                            <VueDatePicker 
                                v-model="filter.date" 
                                format="dd/MM/yyyy" 
                                locale="pt-br"
                                cancelText="cancelar" 
                                selectText="selecione" 
                                :enable-time-picker="false"
                                :partial-range="false"
                                max-range="31"
                                range 
                                utc>
                            </VueDatePicker> 
                        </div>
                        <div class="flex" v-if="props.role === 1 || props.role === 4  || props.role === 3">
                            <Button 
                                variant="default" 
                                type="button" 
                                class="ml-2 py-2.5"
                                @click="openFilter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-sky-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                                </svg>
                            </Button>
                        </div>    
                    </div>


                    <!-- dashboard admin, marketing e gerente-->
                    <div v-if="props.role === 1 || props.role === 3 || props.role === 4"  class="space-y-6"> 
                        <div class="grid sm:grid-cols-2 md:grid-cols-6 gap-4">
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">LEADS NO PERÍODO</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0">{{ data.total }}</h1>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">AGUARDANDO NA FILA</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0">{{ data.pendings }}</h1>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">EM ATENDIMENTO</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0">{{ data.inprogress }}</h1>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">PROPOSTA CRIADA</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0">{{ data.proposals }}</h1>
                                <span class="text-xs text-slate-500 bg-slate-100 w-full flex px-2 py-1">R$ {{ vueNumberFormat(data.dealsOpenTotal, { prefix: '' }) }}</span>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">NEGÓCIO FECHADO</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0">{{ data.deals }}</h1>
                                <span class="text-xs text-slate-500 bg-slate-100 w-full flex px-2 py-1">R$ {{ vueNumberFormat((data.dealsTotal), { prefix: '' }) }}</span>                            
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">NEGÓCIO APROPRIADO </span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0">{{ data.appropriated }}</h1>
                                <span class="text-xs text-slate-500 bg-slate-100 w-full flex px-2 py-1">R$ {{ vueNumberFormat(data.dealsAppropriated, { prefix: '' }) }}</span>
                            </div>
                        </div>
                        
                        <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">ATENDIMENTOS POR DIA</span>
                                <div class="mt-6 lg:h-72 overflow-x-auto">
                                    <ChartBar :data="data.chartLeads[0]"/>
                                </div>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">NEGÓCIOS FECHADOS POR DIA</span>
                                <div class="mt-6 lg:h-72 overflow-x-auto">
                                    <ChartBar :data="data.chartDeals[0]"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- dashboard consultor-->
                    <div v-if="props.role === 2" class="space-y-6"> 
                        <div class="grid sm:grid-cols-2 md:grid-cols-6 gap-4">
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">LEADS NO PERÍODO</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-6xl tracking-tighter sm:py-1 md:py-0">{{ data.total }}</h1>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">EM ATENDIMENTO</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-6xl tracking-tighter sm:py-1 md:py-0">{{ data.inprogress }}</h1>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">PENDENTE</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-6xl tracking-tighter sm:py-1 md:py-0">{{ data.pendings }}</h1>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">PROPOSTA CRIADA</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-6xl tracking-tighter sm:py-1 md:py-0">{{ data.proposals }}</h1>
                                <span class="text-xs text-slate-500 bg-slate-100 w-full flex px-2 py-1">R$ {{ vueNumberFormat(data.dealsOpenTotal, { prefix: '' }) }}</span>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">NEGÓCIO FECHADO</span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-6xl tracking-tighter sm:py-1 md:py-0">{{ data.deals}}</h1>
                                <span class="text-xs text-slate-500 bg-slate-100 w-full flex px-2 py-1">R$ {{ vueNumberFormat(data.dealsTotal, { prefix: '' }) }}</span>                            
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">NEGÓCIO APROPRIADO </span>
                                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-6xl tracking-tighter sm:py-1 md:py-0">{{ data.appropriated }}</h1>
                                <span class="text-xs text-slate-500 bg-slate-100 w-full flex px-2 py-1">R$ {{ vueNumberFormat(data.dealsAppropriated, { prefix: '' }) }}</span>
                            </div>
                        </div>

                        
                        <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">ATENDIMENTOS POR DIA</span>
                                <div class="mt-6 lg:h-72 overflow-x-auto">
                                    <ChartBar :data="data.chartLeads[0]"/>
                                </div>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">NEGÓCIOS FECHADOS POR DIA</span>
                                <div class="mt-6 lg:h-72 overflow-x-auto">
                                    <ChartBar :data="data.chartDeals[0]"/>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4" >
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-2">
                                    AGUARDANDO ATENDIMENTO <Badge v-if="data.waiting.length">{{ data.waiting.length }}</Badge>
                                </span>
                                <div class="mt-6 md:h-72 sm:h-72 overflow-x-auto">
                                    <ul class="space-y-3">
                                        <li 
                                            v-for="msg in data.waiting" 
                                            :key="msg.id"
                                            class="border border-slate-200 border-l-2 border-l-red-500 text-slate-600 px-3 py-1 text-xs whitespace-break-spaces shadow-sm">
                                            <div class="py-2 font-semibold">{{ msg.name }}</div>
                                            
                                            <div class="flex justify-between">
                                                <p class="mb-1 flex">Recebido: {{ moment(msg.created_at).format("DD/MM/YYYY HH:mm") }}</p>
                                                <ButtonLink
                                                    variant="basic"
                                                    class=""
                                                    :value="`/lead/${msg.id}/`"
                                                    >    
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                                    </svg>
                                                </ButtonLink>
                                            </div>
                                        </li>
                                    </ul>
                                    <div v-if="!data.waiting.length" class="flex sm:text-xxs md:text-sm text-slate-600">
                                        🕛 Nenhum lead aguardando atendimento
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-2">
                                    AGENDAMENTOS DA SEMANA
                                    <Badge v-if="data.schedules.length">{{ data.schedules.length }}</Badge>
                                </span>
                                <div class="mt-6 md:h-72 sm:h-72 overflow-x-auto">
                                    <ul class="space-y-3">
                                        <li 
                                            v-for="msg in data.schedules" 
                                            :key="msg.id"
                                            :class="today(msg.schedule_date) ? 'bg-sky-500 text-white' : 'bg-slate-50 text-sky-500 border border-slate-300'"
                                            class="px-3 py-1 text-xs whitespace-break-spaces rounded-md shadow-sm font-semibold">
                                            <div class="py-2 text-xs">{{ msg.name }}</div>
                                            <div class="mb-1 flex justify-between">
                                                <div class="flex space-x-1">
                                                    <svg aria-hidden="true" :class="today(msg.schedule_date) ? 'text-white' : 'text-sky-500'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                                    <span v-if="today(msg.schedule_date)">Hoje às {{ moment(msg.schedule_date).format("HH:mm") }}</span>
                                                    <span v-else>{{ moment(msg.schedule_date).format("DD/MM/YYYY HH:mm") }}</span>
                                                </div>
                                                <div>
                                                    <ButtonLink
                                                        variant="basic"
                                                        class=""
                                                        :value="`/lead/${msg.id}/`"
                                                        >    
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" :class="today(msg.schedule_date) ? 'text-white' : 'text-sky-500'" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                                        </svg>
                                                    </ButtonLink>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <div v-if="!data.schedules.length" class="flex sm:text-xxs md:text-sm text-slate-600">
                                        🕛 Nenhum agendamento criado até o momento
                                    </div>
                                </div>
                            </div>
                            <div v-if="data.funnels.length" class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">FUNIL</span>
                                <div class="flex space-x-2">
                                    <div class="mt-6 w-4/5 space-y-3">
                                        <div class="justify-center flex">
                                            <div class="w-full text-white text-xxs text-center py-4 shadow-md" style="background: rgb(255, 80, 80);">{{ data.funnels[0].name}} = <span class="bg-white text-red-500 px-2">{{ data.funnels[0].total}}</span></div>
                                        </div>                                        
                                        <div class="justify-center flex">
                                            <div class="w-1/2 text-white text-xxs text-center py-2 shadow-md" style="background: rgb(0, 32, 96);">{{ data.funnels[1].name}} = <span class="bg-white text-blue-800 px-2">{{ (data.funnels[1].count + data.funnels[2].count + data.funnels[3].count )}}</span><br><span class="text-xxs py-2 pl-2">{{ vueNumberFormat(((data.funnels[1].count + data.funnels[2].count + data.funnels[3].count ) / (data.funnels[0].total ))*100, { prefix: '' }) }}%</span></div>
                                            <div class="text-xxs py-4 pl-2">50%</div>
                                        </div>
                                        <div class="justify-center flex">
                                            <div class="w-1/3 text-white text-xxs text-center py-2 shadow-md" style="background: rgb(0, 176, 240);">{{ data.funnels[2].name }} = <span class="bg-white text-sky-500 px-2">{{(data.funnels[2].count + data.funnels[3].count)}}</span><br><span class="text-xxs py-2 pl-2">{{ vueNumberFormat(((data.funnels[2].count + data.funnels[3].count) / (data.funnels[1].count + data.funnels[2].count + data.funnels[3].count ))*100, { prefix: '' }) }}%</span></div>
                                            <div class="text-xxs py-4 pl-2">13%</div>
                                        </div>
                                        <div class="justify-center flex">
                                            <div class="w-1/4 text-white text-xxs text-center py-2 shadow-md" style="background: rgb(0, 176, 80);">{{ data.funnels[3].name}} = <span class="bg-white text-green-800 px-2">{{ data.funnels[3].count}}</span><br><span class="text-xxs py-2 pl-2">{{ vueNumberFormat((data.funnels[3].count / (data.funnels[2].count + data.funnels[3].count))*100, { prefix: '' }) }}%</span></div>
                                            <div class="text-xxs py-6 pl-2">31%</div>
                                        </div>
                                    </div>
                                    <div class="mt-6 w-1/5 space-y-3">
                                        <div class="w-full text-white text-xxs text-center py-4 h-12 "></div>
                                        <div class="w-full text-white text-xxs text-center py-2 h-12 "></div>
                                        <div class="w-full text-white text-xxs text-center py-2 h-12 "></div>
                                        <div class="w-full text-white text-xxs text-center py-2 h-12 "></div>
                                        <div class="w-full h-16 flex space-x-2 "><div class="w-2/3 text-white text-xxs font-bold text-center inline-block align-middle py-2 h-16 shadow-md" style="background: rgb(0, 176, 80);"><span class="font-light inline-block">Conversão Geral</span><span class="pt-0.5 pl-2 block">{{ vueNumberFormat((data.funnels[3].count / data.funnels[0].total)*100, { prefix: '' }) }}%</span></div><div class="text-xxs w-1/3 py-6">2%</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    
                    <div v-if="props.role === 1 || props.role === 3 || props.role === 4" class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                            <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">AGUARDANDO ATENDIMENTO</span>
                            <div class="mt-6 lg:h-72 overflow-x-auto">
                                <table class="w-full text-sm text-left  text-slate-50 whitespace-nowrap">
                                    <thead class="font-bold text-xs">
                                        <tr class="bg-sky-900 shadow-sm">
                                            <th scope="col" class="px-2 py-2.5 w-full">
                                                Consultor
                                            </th>
                                            <th scope="col" class="px-2 py-2.5 text-center">
                                                Leads
                                            </th>                                        
                                            <th scope="col" class="px-2 py-2.5 text-center">
                                                Último Lead
                                            </th>                                        
                                        </tr>
                                    </thead>
                                    <tbody class="font-normal text-xs text-slate-500">
                                        <tr
                                            v-for="ranking in waitingRanking"
                                            :key="ranking.id"
                                            class="even:bg-slate-50 odd:bg-white"
                                        >
                                            <td class="px-2 py-2.5">{{ ranking.user[0].name }}</td>
                                            <td class="px-2 py-2.5 text-center">{{ ranking.total }}</td>
                                            <td class="px-2 py-2.5 text-center">{{ moment(ranking.user[0].last_interaction).format("DD/MM/YYYY HH:mm") }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div v-if="data.funnels.length" class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                            <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">FUNIL</span>
                            <div class="flex space-x-2">
                                <div class="mt-6 w-4/5 space-y-3">
                                    <div class="justify-center flex">
                                        <div class="w-full text-white text-xxs text-center py-4 shadow-md" style="background: rgb(255, 80, 80);">{{ data.funnels[0].name}} = <span class="bg-white text-red-500 px-2">{{ data.funnels[0].total}}</span></div>
                                    </div>                                        
                                    <div class="justify-center flex">
                                        <div class="w-1/2 text-white text-xxs text-center py-2 shadow-md" style="background: rgb(0, 32, 96);">{{ data.funnels[1].name}} = <span class="bg-white text-blue-800 px-2">{{ (data.funnels[1].count + data.funnels[2].count + data.funnels[3].count )}}</span><br><span class="text-xxs py-2 pl-2">{{ vueNumberFormat(((data.funnels[1].count + data.funnels[2].count + data.funnels[3].count ) / (data.funnels[0].total ))*100, { prefix: '' }) }}%</span></div>
                                        <div class="text-xxs py-4 pl-2">50%</div>
                                    </div>
                                    <div class="justify-center flex">
                                        <div class="w-1/3 text-white text-xxs text-center py-2 shadow-md" style="background: rgb(0, 176, 240);">{{ data.funnels[2].name }} = <span class="bg-white text-sky-500 px-2">{{(data.funnels[2].count + data.funnels[3].count)}}</span><br><span class="text-xxs py-2 pl-2">{{ vueNumberFormat(((data.funnels[2].count + data.funnels[3].count) / (data.funnels[1].count + data.funnels[2].count + data.funnels[3].count ))*100, { prefix: '' }) }}%</span></div>
                                        <div class="text-xxs py-4 pl-2">13%</div>
                                    </div>
                                    <div class="justify-center flex">
                                        <div class="w-1/4 text-white text-xxs text-center py-2 shadow-md" style="background: rgb(0, 176, 80);">{{ data.funnels[3].name}} = <span class="bg-white text-green-800 px-2">{{ data.funnels[3].count}}</span><br><span class="text-xxs py-2 pl-2">{{ vueNumberFormat((data.funnels[3].count / (data.funnels[2].count + data.funnels[3].count))*100, { prefix: '' }) }}%</span></div>
                                        <div class="text-xxs py-6 pl-2">31%</div>
                                    </div>
                                </div>
                                <div class="mt-6 w-1/5 space-y-3">
                                    <div class="w-full text-white text-xxs text-center py-4 h-12 "></div>
                                    <div class="w-full text-white text-xxs text-center py-2 h-12 "></div>
                                    <div class="w-full text-white text-xxs text-center py-2 h-12 "></div>
                                    <div class="w-full text-white text-xxs text-center py-2 h-12 "></div>
                                    <div class="w-full h-16 flex space-x-2 "><div class="w-2/3 text-white text-xxs font-bold text-center inline-block align-middle py-2 h-16 shadow-md" style="background: rgb(0, 176, 80);"><span class="font-light inline-block">Conversão Geral</span><span class="pt-0.5 pl-2 block">{{ vueNumberFormat((data.funnels[3].count / data.funnels[0].total)*100, { prefix: '' }) }}%</span></div><div class="text-xxs w-1/3 py-6">2%</div></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.role === 1 || props.role === 4  || props.role === 3" class="grid sm:grid-cols-1 md:grid-cols-1 gap-4">
                        <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                            <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">RANKING DE CONSULTORES</span>
                            <div class="mt-6 lg:h-72 overflow-x-auto">
                                <table class="w-full text-sm text-left table-auto text-slate-50 whitespace-nowrap">
                                    <thead class="font-bold text-xs">
                                        <tr class="bg-sky-900 shadow-sm">
                                            <th scope="col" class="px-2 py-2.5">
                                                Consultor
                                            </th>
                                            <th scope="col" class="px-2 py-2.5 text-center">
                                                Leads
                                            </th>   
                                            <th colspan="2" scope="col" class="px-2 py-2.5">
                                                Propostas Criadas
                                            </th>   
                                            <th colspan="2" scope="col" class="px-2 py-2.5">
                                                Negócios Fechados
                                            </th>   
                                            
                                            <th scope="col" class="px-2 py-2.5 text-right">
                                                Conversão
                                            </th>   
                                        </tr>
                                    </thead>
                                    <tbody class="font-normal text-xs text-slate-500">
                                        <tr
                                            v-for="ranking in userRanking"
                                            :key="ranking.id"
                                            class="even:bg-slate-50 odd:bg-white"
                                        >
                                            <td class="px-2 py-2.5">{{ ranking.user[0].name }}</td>
                                            <td class="px-2 py-2.5 text-center">{{ ranking.total }}</td>
                                            <td class="px-2 py-2.5 w-8">{{ranking.proposals }}</td>
                                            <td class="px-2 py-2.5 w-1/6">{{vueNumberFormat(ranking.dealsOpenTotal) }}</td>
                                            <td class="px-2 py-2.5 w-8">{{ ranking.deals }}</td>
                                            <td class="px-2 py-2.5 w-1/6">{{vueNumberFormat(ranking.dealsTotal) }}</td>                                            
                                            <td class="px-2 py-2.5 w-1/12 text-right">{{ vueNumberFormat(((ranking.deals / ranking.total))*100, { prefix: '' }) }}%</td>
                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.role === 1 || props.role === 3" class="grid sm:grid-cols-1 md:grid-cols-1 gap-4">
                        <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                            <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">LEADS POR FORMULÁRIO</span>
                            <div class="mt-6 lg:h-72 overflow-x-auto">
                                <table class="w-full text-sm text-left  text-slate-50 whitespace-nowrap">
                                    <thead class="font-bold text-xs">
                                        <tr class="bg-sky-900 shadow-sm">
                                            <th scope="col" class="px-2 py-2.5 w-2">
                                                Formulário
                                            </th>
                                            <th scope="col" class="px-2 py-2.5 w-2 text-center">
                                                Leads
                                            </th>     
                                            <th scope="col" class="px-2 py-2.5 w-2 text-center">
                                                Negócios
                                            </th>                                        
                                            <th scope="col" class="px-2 py-2.5 w-2 text-right">
                                                Conversão
                                            </th>                                        
                                        </tr>
                                    </thead>
                                    
                                    <tbody class="font-normal text-xs text-slate-500">
                                        <tr
                                            v-for="ranking in formRanking"
                                            :key="ranking.id"
                                            class="even:bg-slate-50 odd:bg-white"
                                        >
                                            <td class="px-2 py-2.5">{{ ranking.name }}</td>
                                            <td class="px-2 py-2.5 text-center w-1/6">{{ ranking.total }}</td>
                                            <td class="px-2 py-2.5 text-center w-1/6">{{ ranking.deals }}</td>
                                            <td class="px-2 py-2.5 text-right w-1/6">{{ vueNumberFormat((ranking.deals / ranking.total)*100, { prefix: '' }) }}%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
                                <div class="grid grid-cols-1 gap-4">
                                    <div  v-if="props.role === 1 || props.role === 3" class="col-span-3">
                                        <InputLabel class="text-xs">Empresa</InputLabel>
                                        <Multiselect
                                            v-model="filter.companies"
                                            :options="companies"
                                            mode="tags"
                                            placeholder="Selecione"
                                        />
                                    </div>
                                    <div class="col-span-3">
                                        <InputLabel class="text-xs">Usuários</InputLabel>
                                        <Multiselect
                                            v-model="filter.users"
                                            :options="data.users"
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
        </template>
    </AuthenticatedLayout>
</template>
