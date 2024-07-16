<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch, reactive, computed, onMounted  } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { throttle, pickBy } from 'lodash';
import { router } from '@inertiajs/vue3';
import { Tabs, Tab } from 'flowbite-vue';
import { Badge } from 'flowbite-vue';
import TextInput from '@/Components/TextInput.vue';
import Link from '@/Components/Link.vue';
import Alert from '@/Components/Alert.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SlideOver from '@/Components/SlideOver.vue';
import Actions from './Partials/LeadActions.vue';
import Proposal from '../Lead/Partials/Proposal.vue';
import Kanban from './Partials/Kanban.vue';
import moment from "moment";
import Multiselect from '@vueform/multiselect';  
import VueDatePicker from '@vuepic/vue-datepicker';
import 'vue3-carousel/dist/carousel.css'
import { defineComponent } from 'vue'
import { Carousel, Navigation, Slide } from 'vue3-carousel'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    leads: Object,
    products: Object,
    origens: Object,
    channels: Object,
    status: Object,
    filters: Object,
    funnelSteps: Object,
    proposalTypes: Object,
    kabanCards: Object,
    users: Object,
    companies: Object,
    role: String,
});

const leadFilter = reactive({
    term: props.filters.term.term,
    product: props.filters.product,
    origem: props.filters.origem,
    channel: props.filters.channel,
    funnelStep: props.filters.funnelStep,
    date: props.filters.date,
    user: props.filters.user,
    companies: props.filters.companies,
    stars: props.filters.stars,
    status: props.filters.status,
});

const filterTab = ref(false);
const proposalShow = ref(false);
const leadProposal = ref([{}]);
const star = ref(5);

const settings = {
    itemsToShow: 1,
    snapAlign: 'start',
};
    
const breakpoints = {
    // 700px and up
    340: {
    itemsToShow: 1,
    snapAlign: 'start',
    },
    // 1024 and up
    1024: {
    itemsToShow: 4,
    snapAlign: 'start',
    },
};

watch(leadFilter,
    throttle( ()=>{
        let query = pickBy(leadFilter);
        let queryRoute = route('funnel.index', Object.keys(query).length ? query : {
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

const newlead = computed(() => {
    let data = props.leads.filter((x) => x.status_id === 9 && x.new_lead === 1);
    return data;   
});

const schedule = computed(() => {
    var currentDate = moment(new Date()).format("YYYY-MM-DD");
    var endDate = new Date(currentDate +" 23:59:50");
    var startDate = new Date(currentDate +" 00:00:00");

    let data = props.leads.filter((x) => x.status_id === 7 && (new Date(x.schedule_date) <= endDate) && (new Date(x.schedule_date) > startDate));
    return data;   
});

const pending = computed(() => {
    var currentDate = moment(new Date()).format("YYYY-MM-DD");
    var startDate = new Date(currentDate +" 00:00:00");

   let data = props.leads.filter((x) => x.status_id === 10 && (new Date(x.interaction_date) < startDate));
   return data;   
});

function clearFilter(){
    router.get('/funil');
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

function closeSlide(){
    proposalShow.value = false;
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
    .multiselect-tag{
        background: #38bdf8 !important; 
    }

    html {
        scroll-behavior: smooth;
    }

.carousel__item {
    height: 100%;
    margin: 0;
}

.carousel__slide {
  padding: 10px;
}

.carousel__prev,
.carousel__next {
    top: -20px;    
    margin: 0;
    background: #bdbdbd36;
    color: #5f6b7d;
    border-radius: 5px;
}
</style>
<template>
    <Head title="Leads" />

    <AuthenticatedLayout>
        <div class="pt-5 max-w-7xl mx-auto sm:px-4 md:px-10 mb-5">
            <div class="fixed bottom-10 right-2 text-blue-500 sm:block md:hidden z-50 ">
                <Link
                    class="px-3 py-3.5 w-10 h-10 bg-sky-500 rounded-full hover:bg-sky-700 active:shadow-lg mouse shadow transition ease-in duration-200 focus:outline-none"
                    value="/lead/criar">
                    <svg viewBox="0 0 20 20" enable-background="new 0 0 20 20" class="w-6 h-6 inline-block">
                        <path fill="#FFFFFF" d="M16,10c0,0.553-0.048,1-0.601,1H11v4.399C11,15.951,10.553,16,10,16c-0.553,0-1-0.049-1-0.601V11H4.601
                                            C4.049,11,4,10.553,4,10c0-0.553,0.049-1,0.601-1H9V4.601C9,4.048,9.447,4,10,4c0.553,0,1,0.048,1,0.601V9h4.399
                                            C15.952,9,16,9.447,16,10z" />
                    </svg>
                </Link>
            </div>

            <Alert v-if="$page.props.flash.message" type="info" @close="onClose()">
                {{ $page.props.flash.message }}
            </Alert>
             <!-- SEARCH -->
            <div class="grid sm:grid-cols-12 md:grid-cols-10 md:gap-2 sm:gap-1 md:mt-10 sm:mt-2 mb-5 text-slate-600 ">
                <div class="col-span-1 md:block sm:hidden">
                    <ButtonLink 
                        variant="primary"
                        class="block h-9"
                        value="/lead/criar">NOVO LEAD 
                    </ButtonLink>
                </div>
                <div class="lg:col-span-4 sm:col-span-8 bg-slate-200">
                    <TextInput
                        type="text"
                        v-model="leadFilter.term"
                        placeholder="Pesquise por nome, email ou telefone"
                        class="h-9 sm:w-full"
                    />
                </div>
                <div class="lg:col-span-3 sm:col-span-8 sm:hidden md:block" >
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
                <div class="flex">
                    <Button 
                            variant="default" 
                            type="button" 
                            class="ml-2 py-2.5 md:flex"
                            @click="openFilter">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-sky-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                        </Button>
                    <Button 
                        variant="default" 
                        type="button" 
                        class="ml-2 py-2.5 md:flex "
                        @click="clearFilter">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </Button>                    
                </div>
            </div>
            <div class="py-8">
                <!-- Kanban columns -->
                <Carousel v-bind="settings" :breakpoints="breakpoints">
                    <template #addons>
                      <Navigation />
                    </template>
                    <Slide
                        v-for="list in kabanCards"
                        :key="list.id"  
                        >
                        <div class="carousel__item">
                            <div class="">
                                <div :id="`board-${list.id}`" :title="list.name" :style="`background:${list.color}`"  :class="`text-white rounded text-md font-semibold p-3 mb-2 justify-between flex`"><span>{{ list.name}}</span><span>{{ list.count }}</span></div>
                                <div class="bg-gray-100 rounded py-4 sm:w-72">
                                    <ul
                                        v-for="element in list.leads"
                                        :key="element.id"
                                        class="mt-3"
                                    >
                                        <li :class="`border-l-2 border-${list.color}-400 bg-white shadow`">
                                            <div class="flex py-2 justify-between">
                                                <div class="mb-2 pl-4 text-left">
                                                    <Link :href="`/lead/${element.id}/`" class="font-bold text-sm text-sky-900">{{ element.name }}</Link>
                                                    <p class="text-xs text-gray-500 w-full">{{ element.product }}</p>
                                                </div>
                                                <div class="flex space-x-1 h-10 pr-1">
                                                    <Actions :lead="element"  @openProposal="openProposal(element)"></Actions>
                                                </div>
                                            </div>
                                            <div class="px-4 py-2 flex justify-between text-slate-400 align-baseline">
                                                <div class="flex space-x-2">
                                                    <span class="bg-slate-50 text-slate-600 py-1 rounded-md text-xs font-light px-2">{{ element.origem }}</span>
                                                </div>
                                                <div class="flex pt-2">
                                                    <p v-if="props.role === 1 || props.role === 4" class="text-xs pl-1">{{ element.usuario }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-1 px-4 py-2 text-xs text-slate-500 border-t border-slate-100 text-left">
                                                <span v-if="list.id === 2 && element.schedule_date != null ">Retornar em {{ moment(element.schedule_date).format("DD/MM/YYYY HH:mm") }}</span>
                                                <span v-else>Recebido em {{ moment(element.created_at).format("DD/MM/YYYY HH:mm") }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </Slide>

                    
                </Carousel>
            </div>
        </div><br>
        
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
                            <div v-if="props.role === 1"  class="grid grid-cols-3 gap-4">
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
                            <div v-if="props.role === 1 || props.role === 4"  class="grid grid-cols-3 gap-4">
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
                        </div>
                    </form>
                </div>  
            </div>
        </Slideover>

        <Proposal :show="proposalShow" page="/funil" :types="proposalTypes" :lead="leadProposal" @closeSlide="closeSlide()" ></Proposal>
    </AuthenticatedLayout>
</template>
