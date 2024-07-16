<script setup>
import { ref  } from 'vue';
import Button from '@/Components/Button.vue';
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import SlideOver from '@/Components/SlideOver.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import Alert from '@/Components/Alert.vue';

const success = ref(false);

const props = defineProps({
    receivable: Object,
    show: Boolean, 
    page: String,
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

const form = useForm({
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

function newReceivable(){
    newTab.value = true;
    success.value = false;
    
    form.id = null;
    form.group = null;
    form.quota = null;
    form.parcel = null;
    form.description = null;
    form.due_date = null;
    form.subtotal = null;
    form.descount = null;
    form.interest = null;
    form.total = null;
}

function calc(){    
    console.log(formatNumber(form.subtotal));
    form.total = formatNumber(form.subtotal)+formatNumber(form.interest)-formatNumber(form.descount);
}

function store(errors){
    form.post(route("receivable.store", form.id),{
        onSuccess: () => successForm(),
    });    
}

function successForm(){
    success.value = true;
    form.reset();
}

const emit = defineEmits(['closeSlide']);

function closeModal() {
    emit('closeSlide');
    success.value = false;
}

function formatNumber(p){
    var n = p.replace('.','');
    n = n.replace(',','.');    
    return parseFloat(n);
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style>
    .multiselect-tag{
        background: #38bdf8 !important; 
    }
</style>
<template>
    <!-- NEW RECEIVABLE -->
    <SlideOver :showSlide="show" title="Nova Conta à Receber" @close="closeModal()">
            <div>
                <div v-if="!success" class="bg-white">
                    <form @submit.prevent="store">
                        <div class="p-5 py-5 space-y-5">
                            <div class="mt-1 grid grid-cols-2 space-x-2">
                                <div>
                                    <TextInput
                                        textLabel="Grupo"
                                        type="text" 
                                        v-model="form.group"
                                        name="group"
                                        class="w-full"
                                        placeholder=""                                        
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
                                    />
                                    <div
                                        v-if="form.errors.quota"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ form.errors.quota }}
                                    </div>
                                </div>
                            </div>                
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-3">
                                    <InputLabel class="text-xs">Vencimento</InputLabel>
                                    <VueDatePicker 
                                        v-model="form.due_date" 
                                        format="dd/MM/yyyy" 
                                        locale="pt-br"
                                        cancelText="cancelar" 
                                        selectText="selecione" 
                                        :enable-time-picker="false"
                                        utc>
                                    </VueDatePicker>
                                    <div
                                        v-if="form.errors.due_date"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ form.errors.due_date }}
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
                                    v-model.lazy="form.subtotal"
                                    name="subtotal"
                                    v-money3="config"
                                    placeholder="0,00"
                                    @blur="calc"                                                                           
                                />
                                <div
                                    v-if="form.errors.subtotal"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.subtotal }}
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
                                    v-model.lazy="form.descount"
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
                                    v-model.lazy="form.interest"
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
                                    v-model.lazy="form.total"
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
                            <div v-if="$page.props.errors" class="col-span-3 text-xs text-red-500 pr-0">
                                <div v-for="(v, k) in $page.props.errors" :key="k">  
                                    <div v-if="k != 'file' && k != 'date'">
                                        <Alert type="danger">
                                            <b>Atenção:</b> {{ v }}
                                        </Alert>
                                    </div>
                                </div>
                            </div>
                            <div class="sm:col-span-4 space-x-2">
                                <Button
                                    variant="primary"
                                    type="submit"
                                    :disabled="form.processing"
                                    :class="{ 'opacity-25': form.processing }"
                                >
                                    SALVAR
                                </Button>   
                                <Button
                                    variant="default"
                                    type="button"
                                    @click="closeModal"
                                >
                                    CANCELAR
                                </Button>                       
                            </div>
                        </div>
                    </form>
                </div>  
            </div>
            <div v-if="success" class="text-center mt-10 h-screen align-middle">
                <h1 class="text-lg font-extralight">Conta à receber cadatrada com sucesso</h1>
                <div class="mt-6 grid grid-cols-1 gap-x-6 px-5">
                    <div class="sm:col-span-4 space-x-2">
                        <Button
                            variant="primary"
                            type="button"
                            @click="closeModal"
                        >
                            FECHAR
                        </Button>                         
                    </div>
                </div> 
            </div>
        </Slideover>
</template>

