<script setup>
import {ref, computed, onMounted } from 'vue';
import moment from "moment";
import { useForm } from '@inertiajs/vue3';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    lead: Object,
    schedules: Object,
});

const form = useForm({
    date: null,
    description: null,
    schedules: null,
    lead_id: null,
});

const datepicker = ref();
const schedulesList = props.schedules;

const scheduleData = computed(() => {
   let data = schedulesList.slice().sort((a, b) => new Date(a.created_at) + new Date(b.created_at));
   return data;   
});

function makeSchedule(){
    
    if(form.description === null){
        return false;
    }
        
    schedulesList.push({id: 0, schedule_date: moment(form.date).format('YYYY-MM-DD HH:mm'), description: form.description, created_at: moment().format('YYYY-MM-DD HH:mm')});
    
    form.schedules = schedulesList;
    form.lead_id = props.lead.id;
    form.post('/lead-schedule', form);
    form.reset();   
}

function editSchedule(index, msg){
    form.date = msg.schedule_date;
    form.description = msg.description;
    schedulesList.splice(index, 1);
}

function quickText(p){
    form. description = p;
}

</script>
<template>
    <div class="mt-5">
        <div class="bg-white">
            <form @submit.prevent="makeSchedule">
                <div>
                    <div>
                        <div class="py-6 px-6 lg:h-72 sm:max-h-80 overflow-x-auto border border-slate-100 rounded-md">
                            <ul class="space-y-3">
                                <li 
                                    v-for="(msg, index) in scheduleData" 
                                    :key="msg.id"
                                    class="bg-sky-400 text-white px-3 py-1 text-xs whitespace-break-spaces shadow-sm">
                                    <div class="py-2 text-xs justify-between flex">
                                        <div>{{ lead.name }}</div>
                                        <Button
                                            v-if="moment(msg.created_at).format('YYYY-MM-DD') === moment().format('YYYY-MM-DD')"
                                            variant="basic"
                                            class="text-white hover:text-slate-100"
                                            @click.prevent="editSchedule(index, msg)"
                                            title="Editar">    
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>   
                                        </Button>
                                    </div>
                                    <div class="pb-2 font-semibold text-sm">{{ msg.description }}</div>
                                    <p class="mb-1 flex">
                                        <svg aria-hidden="true" class="w-3.5 h-3.5 text-white dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                        {{ moment(msg.schedule_date).format("DD/MM/YYYY HH:mm") }}
                                    </p>
                                    <p v-if="msg.user_name" class="pt-2 text-xxs text-sky-100 text-right">Criado por {{ msg.user_name}} {{ moment(msg.created_at).format("DD/MM/YYYY HH:mm") }}</p>
                                    <p v-else class="pt-2 text-xxs text-sky-100 text-right">Criado por {{ $page.props.auth.user.name }} {{ moment(msg.created_at).format("DD/MM/YYYY HH:mm") }}</p>
                                    
                                </li>
                            </ul>
                            <div v-if="!schedules.length" class="flex text-sm text-slate-600 pt-2">
                                🕛 Nenhum agendamento criado até o momento
                            </div>
                        </div>  
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="col-span-3">
                            <Inputlabel
                                for="data"
                                class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                >Data
                            </Inputlabel>
                            <VueDatePicker 
                                v-model="form.date" 
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
                                v-if="form.errors.date"
                                class="text-sm text-red-600"                                    >
                                {{ form.errors.date }}
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mt-4">
                        <div class="col-span-3">
                            <Inputlabel
                                for="description"
                                class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                >Descrição
                            </Inputlabel>
                            
                            <textarea
                                v-model="form.description"
                                name="description"
                                class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                rows="3" 
                                >
                            </textarea>
                            <div
                                v-if="form.errors.description"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </div>
                        </div>
                        
                    </div>                            
                    <div class="w-full flex space-x-2">
                        <span class="inline-block border border-sky-500 text-sky-500 px-2 py-1 rounded-md text-xs shadow-sm cursor-pointer" @click="quickText('Liguei, não atendeu');">Liguei, não atendeu</span>
                        <span class="inline-block border border-sky-500 text-sky-500 px-2 py-1 rounded-md text-xs shadow-sm cursor-pointer" @click="quickText('Retornar mais tarde');">Retornar mais tarde</span>
                        <span class="inline-block border border-sky-500 text-sky-500 px-2 py-1 rounded-md text-xs shadow-sm cursor-pointer" @click="quickText('Retornar amanhã');">Retornar amanhã</span>
                    </div>
                </div>
                <div class="my-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:mb-12">
                    <div class="col-span-1 space-x-3">
                        <Button
                            variant="primary"
                            type="submit"
                            class=""
                            :disabled="form.processing"
                            :class="{ 'opacity-25': form.processing }"
                        >
                            CRIAR
                        </Button>
                    </div>
                </div> 
            </form>
        </div>  
    </div>
</template>