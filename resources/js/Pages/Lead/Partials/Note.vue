<script setup>
import {ref, computed, onMounted } from 'vue';
import moment from "moment";
import { useForm } from '@inertiajs/vue3';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';

const props = defineProps({
    lead: Object,
    notes: Object
});
    
const form = useForm({
    description: null,
    lead_id: null,
});

function makeSchedule(){
    form.lead_id = props.lead.id;
    form.post('/lead-note', form);
    form.reset();
}

</script>
<template>
    <div class="mt-5">
        <div class="bg-white">
            <form @submit.prevent="makeSchedule">
                <div>
                    <div >
                        <div class="py-6 px-6 lg:h-72 sm:max-h-80  overflow-x-auto border border-slate-100 rounded-md">
                            <ul class="space-y-3">
                                <li 
                                    v-for="msg in notes" 
                                    :key="msg.id"
                                    class="bg-yellow-100 text-slate-900 px-3 py-4 text-xs whitespace-break-spaces shadow-sm">
                                    <div class="pb-2 font-normal text-sm">{{ msg.description }}</div>
                                    <p class="pt-2 text-xxs text-slate-900 text-right">Criado por {{ msg.user_name}} {{ moment(msg.created_at).format("DD/MM/YYYY HH:mm") }}</p>
                                </li>
                            </ul>
                            <div v-if="!notes.length" class="flex text-sm text-slate-600 pt-2">
                                🕛 Nenhuma nota criada até o momento
                            </div>
                        </div>  
                    </div>
                    
                    <div class="mt-4 grid grid-cols-3 gap-4">
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
                </div>
                <div class="my-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:mb-12">
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
                </div><br> 
            </form>
        </div>  
    </div>
</template>