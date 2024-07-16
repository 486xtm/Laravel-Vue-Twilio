<script setup>
import { ref, onMounted, computed, watch, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import moment from "moment";
import VueDatePicker from '@vuepic/vue-datepicker';
import { format, unformat } from 'v-money3';
import Alert from '@/Components/Alert.vue';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    proposal: Object,
    lead: Object,
    types: Object,
    products: Object,
    users: Object,
    receivables: Object,
});

const breadcrumb = ref({link:{id: 1, name: 'Propostas', url : "/proposta"}});
const selectedTab = ref(0);
const parcels = ref(0);

let receivable = reactive(props.receivables);
let comission = ref(0);
let comissionTotal = ref(props.proposal.comission_total);
let parcelsTotal = ref(0);
const alertParcels = ref(false);

const form = useForm({
   group: props.proposal.group,
   quota: props.proposal.quota,
   proposal_type: props.proposal.proposal_type,
   product: props.proposal.product_id,
   price:  props.proposal.price,
   comission:  props.proposal.comission,
   comission_total:  props.proposal.comission_total,
   observation: props.proposal.observation,
   parcels: props.proposal.parcels,
   due_date: props.proposal.due_date,
   created_at: props.proposal.created_at,
   approved_at: props.proposal.approved_at,
   user: props.proposal.user_id,
   status_id: props.proposal.status_id,
   receivables: receivable,
});

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

function calc(){
    
    let days = 0;
    let subtotal = unformat(form.price, config) * (unformat(form.comission, config)/100);
    let parcel = parseFloat(subtotal).toFixed(2) / parseInt(form.parcels);
    
    comissionTotal.value = subtotal;
    parcel = parcel.toFixed(2);
    parcelsTotal.value = 0;    
    receivable.splice(0);

    for(let i = 0; i < form.parcels; i++){
        receivable.push({id: i+1, due_date: moment(form.due_date).add(days,'days').format('YYYY-MM-DD'), total: parcel });
        days += 30; 
        parcelsTotal.value += parseFloat(parcel);        
    }

    form.receivables = receivable;
    form.comission_total = subtotal;
    parcels.value = form.parcels;

    //console.log(receivable);
}

function updateParcels(p){

    if(p != undefined){
        parcelsTotal.value = 0;

        receivable.forEach((value, index) => {
            if(value.id === p.id){
                value.total = p.total;
            }
            
            parcelsTotal.value += unformat(value.total, config);
        });
    }
       
    //console.log(receivable);
}

function totalParcels(){
    parcelsTotal.value = 0;

    receivable.forEach((value, index) => {
        parcelsTotal.value += unformat(value.total, config);
    });
    //console.log(receivable);
}

function updateProposal(){
    
    if(parseFloat(comissionTotal.value).toFixed(2) != parseFloat(parcelsTotal.value).toFixed(2)){
        alertParcels.value = true;
        return false;
    }       
    form.put(route("proposal.update", props.proposal.id));
};

function changeTab(index) {
  selectedTab.value = index
}

onMounted(() => {
    totalParcels();
});

</script>

<template>
    <Head title="Proposta" /> 
    <AuthenticatedLayout>
        <div class="pt-5 max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" type="back" class="pb-4"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="px-10 py-6 bg-white border-b border-gray-200">
                    <h2 class="text-base font-semibold leading-7 text-sky-900 mb-1">Informações da Proposta 
                        <span class="bg-sky-500 text-white text-xs font-light py-1 px-2 rounded-md" v-if="proposal.status_id === 11">Aprovada {{ moment(proposal.approved_at).format("DD/MM/YYYY") }}</span>
                        <span class="bg-green-500 text-white text-xs font-light py-1 px-2 rounded-md" v-if="proposal.status_id === 13">Apropriada {{ moment(proposal.appropriated_at).format("DD/MM/YYYY") }}</span>
                    </h2>
                    <div class="py-2 mb-8">
                        <div class="bg-sky-50 p-5 rounded-md space-y-1">
                            <p class="font-semibold text-sky-700">{{ lead[0].name }}</p>
                            <div class="space-x-2 text-slate-600">
                                <span class="text-xs"><b>Produto:</b> {{ lead[0].product }}</span>
                                <span class="text-xs"><b>Origem:</b> {{ lead[0].origem }}/{{ lead[0].channel }}</span>
                            </div>
                        </div>
                    </div>
                    <form @submit.prevent="updateProposal">
                        <div class="space-y-4">
                            <TabGroup :selectedIndex="selectedTab" @change="changeTab">
                                <TabList class=" border-b text-sm border-slate-200 rounded-sm sm:hidden md:block">
                                    <!-- Use the `selected` state to conditionally style the selected tab. -->
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                                        class="px-6 py-2"
                                        >
                                        Geral
                                        </button>
                                    </Tab>
                                    <Tab as="template" v-slot="{ selected }" v-if="$page.props.user.roles[0] === 'admin'">
                                        <button
                                        :class="{ 'border-b-2 border-sky-500 text-sky-500': selected, 'text-slate-500': !selected }"
                                        class="px-6 py-2"
                                        >
                                        Comissão
                                        </button>
                                    </Tab>                                    
                                    <!-- ... -->
                                </TabList>
                                <TabPanels>
                                    <TabPanel>
                                        <div class="space-y-4">
                                            <div class="mt-3 grid grid-cols-2 space-x-2">
                                                <div>
                                                    <TextInput
                                                        textLabel="Grupo"
                                                        type="text"
                                                        v-model="form.group"
                                                        name="group"
                                                        class="w-full"
                                                        placeholder=""
                                                        :disabled="proposal.status_id != 4"
                                                    />
                                                    <div
                                                        v-if="form.errors.group"
                                                        class="text-sm text-red-500 mt-2"
                                                    >
                                                        {{ form.errors.group }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <TextInput
                                                        textLabel="Cota"
                                                        type="text"
                                                        v-model="form.quota"
                                                        name="quota"
                                                        class="w-full"
                                                        placeholder=""
                                                        :disabled="proposal.status_id != 4"
                                                    />
                                                    <div
                                                        v-if="form.errors.quota"
                                                        class="text-sm text-red-500 mt-2"
                                                    >
                                                        {{ form.errors.quota }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <InputLabel
                                                    for="Tipos"
                                                    class="block mb-2 text-sm text-slate-600 dark:text-slate-300"
                                                    >Tipo
                                                </InputLabel>
                                                <select
                                                    v-model="form.proposal_type"
                                                    name="proposal_type"
                                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                                    :disabled="proposal.status_id != 4"
                                                    >
                                                    <option :value="null">Selecione</option>
                                                    <option 
                                                        v-for="item in types"
                                                        :key="item.id" 
                                                        :value="`${item.id}`">
                                                        {{ item.name }}
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="form.errors.proposal_type"
                                                    class="text-sm text-red-600"
                                                >
                                                    {{ form.errors.proposal_type }}
                                                </div>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <InputLabel
                                                    for="Tipos"
                                                    class="block mb-2 text-sm text-slate-600 dark:text-slate-300"
                                                    >Produto
                                                </InputLabel>
                                                <select
                                                    v-model="form.product"
                                                    name="proposal_type"
                                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                                    :disabled="proposal.status_id != 4"
                                                    >
                                                    <option :value="null">Selecione</option>
                                                    <option 
                                                        v-for="item in products"
                                                        :key="item.id" 
                                                        :value="`${item.id}`">
                                                        {{ item.name }}
                                                    </option>
                                                </select>
                                                <div
                                                    v-if="form.errors.product"
                                                    class="text-sm text-red-600"
                                                >
                                                    {{ form.errors.product }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border-b border-slate-900/10 pb-8 mt-5">
                                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2 md:grid-cols-4">
                                                <div>
                                                    <label
                                                        for="textLabel"
                                                        class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                                        >Valor
                                                    </label>
                                                    <input
                                                        class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                                        v-model.lazy="form.price"
                                                        name="price"
                                                        v-money3="config"
                                                        placeholder="0,00"
                                                        :disabled="proposal.status_id != 4"
                                                    />
                                                    <div
                                                        v-if="form.errors.price"
                                                        class="text-sm text-red-500 mt-2"
                                                    >
                                                        {{ form.errors.price }}
                                                    </div>
                                                </div>                                                          
                                            </div>
                                        </div>
                                        
                                        <div class="mt-5" v-if="$page.props.user.roles[0] === 'admin'">
                                            <h2 class="text-base font-semibold leading-7 text-sky-900">Data(s)</h2>
                                            <div class="grid grid-cols-6 gap-4 mt-4">
                                                <div class="col-span-3">
                                                    <Inputlabel
                                                        for="data"
                                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                                        >Data de criação
                                                    </Inputlabel>
                                                    <VueDatePicker 
                                                        v-model="form.created_at" 
                                                        format="dd/MM/yyyy HH:mm" 
                                                        locale="pt-br" 
                                                        cancelText="cancelar" 
                                                        selectText="selecione"
                                                        auto-apply 
                                                        partial-flow 
                                                        :flow="['calendar', 'time']"
                                                        :highlight-week-days="[0, 6]"
                                                    >
                                                    </VueDatePicker>
                                                    <div
                                                        v-if="form.errors.created_at"
                                                        class="text-sm text-red-600"                                    >
                                                        {{ form.errors.created_at }}
                                                    </div>
                                                </div>
                                                <div class="col-span-3" v-if="proposal.status_id === 11">
                                                    <Inputlabel
                                                        for="data"
                                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                                        >Data de Aprovação
                                                    </Inputlabel>
                                                    <VueDatePicker 
                                                        v-model="form.approved_at" 
                                                        format="dd/MM/yyyy HH:mm" 
                                                        locale="pt-br" 
                                                        cancelText="cancelar" 
                                                        selectText="selecione"
                                                        auto-apply 
                                                        partial-flow 
                                                        :flow="['calendar', 'time']"
                                                        :highlight-week-days="[0, 6]"
                                                    >
                                                    </VueDatePicker>
                                                    <div
                                                        v-if="form.errors.approved_at"
                                                        class="text-sm text-red-600"                                    >
                                                        {{ form.errors.approved_at }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <h2 class="text-base font-semibold leading-7 text-sky-900">Consultor(a)</h2>

                                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-10">
                                                <div class="sm:col-span-6">
                                                    <Inputlabel
                                                        for="User"
                                                        class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                                        >Nome
                                                    </Inputlabel>
                                                    <select
                                                        v-model="form.user"
                                                        name="user"
                                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full" 
                                                        :disabled="proposal.status_id != 4"                                    
                                                        >
                                                        <option :value="null">Selecione</option>
                                                        <option 
                                                            v-for="user in users"
                                                            :key="user.id" 
                                                            :value="`${user.id}`">
                                                            {{ user.name }}
                                                        </option>
                                                    </select>
                                                    <div
                                                        v-if="form.errors.user"
                                                        class="text-sm text-red-600"
                                                    >
                                                        {{ form.errors.user }}
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-10">
                                                    <Inputlabel
                                                        for="Observação"
                                                        class="block mb-2 text-sm text-slate-900 dark:text-slate-300"
                                                        >Observação
                                                    </Inputlabel>
                                                    <textarea
                                                        v-model="form.observation"
                                                        name="observation"
                                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                                        rows="3"
                                                        :disabled="proposal.status_id != 4" 
                                                        >
                                                    </textarea>
                                                    <div
                                                        v-if="form.errors.observation"
                                                        class="text-sm text-red-600"
                                                    >
                                                        {{ form.errors.observation }}
                                                    </div>
                                                </div>                      
                                            </div>
                                        </div>
                                    </TabPanel>
                                    <TabPanel class="py-0">
                                        <div v-if="$page.props.user.roles[0] === 'admin'" class="border-b border-slate-900/10 pb-10 mt-5">
                                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2 md:grid-cols-4">
                                                <div>
                                                    <label
                                                        for="textLabel"
                                                        class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                                        >Comissão(%)
                                                    </label>
                                                    <input
                                                        class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                                        v-model.lazy="form.comission"
                                                        name="comission"
                                                        v-money3="config"
                                                        placeholder="0,00"
                                                        maxlength="6"
                                                        :disabled="proposal.status_id != 4 && $page.props.user.roles[0] != 'admin'"
                                                    />
                                                    <div
                                                        v-if="form.errors.comission"
                                                        class="text-sm text-red-500 mt-2"
                                                    >
                                                        {{ form.errors.comission }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <TextInput
                                                        textLabel="Parcelamento"
                                                        type="text"
                                                        v-model="form.parcels"
                                                        name="parcels"
                                                        class="w-full"
                                                        placeholder=""
                                                        :disabled="proposal.status_id != 4 && $page.props.user.roles[0] != 'admin'"
                                                    />
                                                    <div
                                                        v-if="form.errors.parcels"
                                                        class="text-sm text-red-500 mt-2"
                                                    >
                                                        {{ form.errors.parcels }}
                                                    </div>
                                                </div>   
                                                <div class="">
                                                    <Inputlabel
                                                        for="data"
                                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                                        >Primeiro Vencimento
                                                    </Inputlabel>
                                                    <VueDatePicker 
                                                        v-model="form.due_date" 
                                                        format="dd/MM/yyyy" 
                                                        locale="pt-br" 
                                                        cancelText="cancelar" 
                                                        selectText="selecione"
                                                        :teleport="true"
                                                        auto-apply 
                                                        partial-flow 
                                                        :enable-time-picker="false"
                                                        :highlight-week-days="[0, 6]"
                                                    >
                                                    </VueDatePicker>
                                                    <div
                                                        v-if="form.errors.date"
                                                        class="text-sm text-red-600"                                    >
                                                        {{ form.errors.date }}
                                                    </div>
                                                </div>
                                                <div class="pt-7">
                                                    <Button
                                                        variant="default"
                                                        type="button"
                                                        class="h-10"
                                                        @click="calc"                                                        
                                                    >
                                                        GERAR CONTAS
                                                    </Button>
                                                </div>  
                                                <div>
                                                    <span class="text-xs text-slate-700 font-bold">Comissão: {{ vueNumberFormat(comissionTotal) }}</span>
                                                </div>                                                             
                                            </div>

                                            <div v-if="receivable.length" class="">
                                                <p class="mt-4 py-2 text-sm">Recebíveis:</p>
                                                <table class="w-1/3 mt-4 font-light text-sm text-left">
                                                    <thead class="bg-slate-200 text-slate-700">
                                                        <th class="py-2 px-2 w-8">N°</th>
                                                        <th class="py-2 px-2 w-full text-center">Valor (R$)</th>
                                                        <th class="py-2 px-2 w-1/3">Vencimento</th>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(item, index) in receivable"
                                                            :key="item.id"
                                                        >
                                                            <td class="py-2 px-2">{{ index + 1}}</td>
                                                            <td class="py-2 px-2 text-center w-96">
                                                                <input
                                                                class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                                                v-model.lazy="item.total"
                                                                name="item_total"
                                                                v-money3="config"
                                                                @blur="updateParcels(item)"
                                                                placeholder="0,00"                                                                
                                                                />
                                                            </td>                                                                
                                                            <td class="py-2 px-2">
                                                                <input type="date" 
                                                                v-model="item.due_date" 
                                                                class="border border-slate-300 px-1.5 py-2 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400" 
                                                                />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="pt-2">
                                                    <span class="text-xs text-slate-700 font-bold">Total do Parcelamento: {{ vueNumberFormat(parcelsTotal) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </TabPanel>                                    
                                    <Alert v-if="alertParcels" type="danger" @close="onClose()" class="mt-4">
                                        <p>A soma das parcelas é diferente do total da comissão</p>
                                    </Alert>
                                </TabPanels>
                            </TabGroup>
                        </div>
                        <div class="pt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-4 space-x-2">
                                <Button
                                    v-if="proposal.status_id === 4 || ($page.props.user.roles[0] === 'admin' && proposal.status_id === 11)"
                                    variant="primary"
                                    type="submit"
                                    class=""
                                    :disabled="form.processing"
                                    :class="{ 'opacity-25': form.processing }"
                                >
                                    SALVAR
                                </Button>
                                <ButtonLink 
                                    variant="default"
                                    class=""
                                    value="/proposta">VOLTAR
                                </ButtonLink>
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div><br><br>
        </div>
    </AuthenticatedLayout>
</template>
