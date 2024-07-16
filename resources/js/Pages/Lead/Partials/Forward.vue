<script setup>
import { ref, watch,  reactive, computed, onMounted  } from 'vue';
import Button from '@/Components/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import SlideOver from '@/Components/SlideOver.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import Multiselect from '@vueform/multiselect';  
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import '@vuepic/vue-datepicker/dist/main.css'

const success = ref(false);
const users = ref({});

const props = defineProps({
    show: Boolean, 
    companies: Object,
    users: Object,
    page: String,
    status: Object,
});

const form = useForm({
   company_from: null,
   user_from: null,
   company_to: null,
   user_to: null,   
   date_forward: null,
   status: null,
});

function getUsers(){
    axios.get("/usuario/listar")
    .then((res) => {
        users.value = res.data;
    })
    .catch((error) => {
            console.log(error);
    });
}


function send(errors){    
    form.put('/leads-encaminhados',{
        onSuccess: () => successForm(),
    });
};

function successForm(){
    success.value = true;
    form.reset();
}

const userFrom = computed(() => {
    var data = "";
    var from = users.value;

    data = from.filter((x) => parseInt(x.company_id) === parseInt(form.company_from));    
    
    if(data.length === 0){
        return "";
    }else{
        return data;   
    }
});

const userTo = computed(() => {
    var data = "";    
    var to = users.value;

    data = to.filter((x) => parseInt(x.company_id) === parseInt(form.company_to));    
    
    if(data.length === 0){
        return "";
    }else{
        return data;   
    }
    
});

function clearFrom(){
    if(form.company_from != ""){
        form.user_from = [];        
    }
        
}

function clearTo(){
    if(form.company_to != "")
        form.user_to= [];
}

const emit = defineEmits(['closeSlide']);

function closeForward() {
    emit('closeSlide');
    success.value = false;
}

onMounted(() => {
    getUsers();
});

</script>
<template>
    <SlideOver :showSlide="show" title="Encaminhar Leads" @close="closeForward">
        <div v-if="!success" class="text-slate-700">
            <div class="bg-white">
                <form @submit.prevent="send">
                    <div class="px-5 space-y-3">
                        <div class="mt-1 grid grid-cols-1 space-x-2">
                            <span class="font-semibold mt-4">Filtros:</span>
                        </div>                           
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-3">
                                <InputLabel class="text-xs">Período</InputLabel>
                                <VueDatePicker 
                                    v-model="form.date_forward" 
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
                        <div class="mt-1 grid grid-cols-1 space-x-2">                           
                            <div>
                                <InputLabel
                                    for="Tipos"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Status:
                                </InputLabel>                            
                                <select
                                    v-model="form.status"
                                    name="product"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                    >
                                    <option :value="null">Todos</option>
                                    <option 
                                        v-for="item in status"
                                        :key="item.id" 
                                        :value="`${item.value}`">
                                        {{ item.label }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.company_from"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.company_from }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-1 grid grid-cols-1 space-x-2">
                            <span class="font-semibold mt-4">De:</span>
                        </div>                           
                        <div class="mt-1 grid grid-cols-1 space-x-2">                           
                            <div>
                                <InputLabel
                                    for="Tipos"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Empresa:
                                </InputLabel>                            
                                <select
                                    v-model="form.company_from"
                                    name="product"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                    @change="clearFrom"
                                    >
                                    <option :value="null">Selecione</option>
                                    <option 
                                        v-for="item in companies"
                                        :key="item.id" 
                                        :value="`${item.value}`">
                                        {{ item.label }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.company_from"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.company_from }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-1 grid grid-cols-1 space-x-2">                           
                            <div>
                                <InputLabel
                                    for="Tipos"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Usuários:
                                </InputLabel>                            
                                <Multiselect
                                        v-model="form.user_from"
                                        :options="userFrom"
                                        mode="tags"                                        
                                        placeholder="Selecione"
                                    />
                            </div>
                        </div>
                        <div class="mt-1 grid grid-cols-1 space-x-2">
                            <span class="font-semibold mt-4">Para:</span>
                        </div>                           
                        <div class="mt-1 grid grid-cols-1 space-x-2">                           
                            <div>
                                <InputLabel
                                    for="Tipos"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    @change="clearTo"
                                    >Empresa:
                                </InputLabel>                            
                                <select
                                    v-model="form.company_to"
                                    name="product"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                    >
                                    <option :value="null">Selecione</option>
                                    <option 
                                        v-for="item in companies"
                                        :key="item.id" 
                                        :value="`${item.value}`">
                                        {{ item.label }}
                                    </option>
                                </select>                                
                            </div>
                        </div>
                        <div class="mt-1 grid grid-cols-1 space-x-2">                           
                            <div>
                                <InputLabel
                                    for="Tipos"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Usuários:
                                </InputLabel>                            
                                <Multiselect
                                        v-model="form.user_to"
                                        :options="userTo"
                                        mode="tags"
                                        placeholder="Selecione"
                                    />
                            </div>
                        </div>                        
                    </div>
                    <div class="mt-6 grid grid-cols-1 gap-x-6 px-5">
                        <div class="sm:col-span-4 space-x-2">
                            <Button
                                variant="primary"
                                type="submit"
                                :disabled="form.processing"
                                :class="{ 'opacity-25': form.processing }"
                            >
                                ENVIAR
                            </Button>                         
                        </div>
                    </div> 
                </form>
            </div>  
        </div>
        <div v-if="success" class="text-center mt-10 h-screen align-middle">
            <h1 class="text-lg font-extralight">{{ $page.props.flash.message }}</h1>
            <div class="mt-6 grid grid-cols-1 gap-x-6 px-5">
                <div class="sm:col-span-4 space-x-2">
                    <Button
                        variant="primary"
                        type="button"
                        @click="closeForward"
                    >
                        FECHAR
                    </Button>                         
                </div>
            </div> 
        </div>
    </SlideOver>
</template>

