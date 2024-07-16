<script setup>
import { ref, watch,  computed, onMounted  } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import Link from '@/Components/Link.vue';
import Button from '@/Components/Button.vue';
import Actions from './LeadActions.vue';
import { replace } from 'lodash';
import moment from "moment";

const star = ref(5);

const props = defineProps({
    lead: Object,
    funnelSteps: Object,
    users: Object,
    role: String,
});

const form = useForm({
    funnelStep: props.lead.funnel_step_id,
    lead_id: props.lead.id,
    user: props.lead.user_id,
});

const formUser = useForm({
    lead_id: props.lead.id,
    user_id: props.lead.user_id,
});

const archiveForm = useForm({
    status: null,
});

const starForm = useForm({
    number: null,
});

const favoriteForm = useForm({
    favorite: null,
});

const letters = computed(() => {
   var name = props.lead.name.split(" ");
   
   if(name.length > 1)
        var fist = name[0].substring(0,1) +name[1].substring(0,1);
    else
        var fist = name[0].substring(0,2);

    return fist;   
});

const whatsappLink = computed(() => {
    var cel = props.lead.celular.replace(/^(\+)|\D/g,'');
    var currentHour = parseInt(moment(new Date()).format("HH"));
    
    if(currentHour < 12)
        var saudacao = "bom dia";
    else if(currentHour < 18)
        var saudacao = "bom tarde";
    else if(currentHour < 23)
        var saudacao = "boa noite";

    if(window.innerWidth > 640)
        var link = "https://web.whatsapp.com/send?phone=+55"+cel+"&text=Olá "+ props.lead.name +", "+saudacao+"!";
    else
        var link = "https://wa.me/+55"+cel+"?text=Olá "+ props.lead.name +", "+saudacao+"!";;

   return link;   
});

function onChange(e) {
    form.funnelStep = e.target.value;
    form.put(route("lead.funnelstep", form));    
}

function setContactFunnel(p) {
    form.funnelStep = p;
    form.put(route("lead.funnelstep", form));    
}

function onChangeUser(e, lead) {
    formUser.user_id = e.target.value;
    formUser.put(route("lead.forward", lead.id));    
}

function setStars(lead, n){
   starForm.number = n;
   starForm.put(route("lead.star", lead.id));
}

function setFavorite(lead){
   favoriteForm.favorite = !lead.favorite;
   favoriteForm.put(route("lead.favorite", lead.id));
}

onMounted(() => {
    //console.log(cel.replace(/^(\+)|\D/g,''));
});

</script>

<template>
    <div class="flex space-x-3">
        <div>
            <div class="p-4 text-white bg-amber-400 rounded-full uppercase">{{ letters }}</div>
        </div>
        <div>
            <h2>{{ lead.name }}</h2>    
            <div v-if="lead.status_id === 8" class="flex">
                <span class="bg-slate-400 text-white text-xxs rounded-md px-2 pt-0.5">ARQUIVADO</span>
            </div>
            <a
                class="text-xxs hover:text-slate-600"
                :href="`mailto:${lead.email}`"
                title="Email">    
                {{ lead.email }}
            </a>
            <p v-if="lead.celular" class="text-xs">{{ lead.celular }}</p>    
            
        </div>
    </div>
    <div class="mt-4 p-2 bg-slate-50 border border-slate-200 rounded-md">
        <div class="flex px-4 space-x-2 text-xs justify-between">
            <Link
                class="hover:text-slate-600"
                :href="`/lead/${lead.id}/editar`"
                title="Editar">    
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>   
            </Link>
            <a
                class="text-sky-500 hover:text-slate-600"
                :href="whatsappLink"
                title="Whatsapp"
                target="_blank">    
                <span class="text-sm"><font-awesome-icon class="w-5 h-5" icon="fa-brands fa-whatsapp" /></span>
            </a>
            <Button
                v-if="lead.status_id != 8 && lead.status_id != 9"
                variant="inline"
                style="margin-top:-5px"
                @click="$emit('openProposal', lead)"
                title="Nova Proposta">    
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </Button>
            <Button
                v-if="lead.status_id != 8"
                variant="inline"
                class="hover:text-slate-600"                
                @click="$emit('openArchive', lead)"
                title="Arquivar">    
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </Button>
            <Link
                class="hover:text-slate-600"
                href="#"
                @click="setFavorite(lead)"
                title="Favoritar">    
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" :class="`${ lead.favorite ? 'text-amber-500' : 'text-slate-500'} w-5 h-5 hover:text-amber-500`">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                </svg>
            </Link>
        </div>        
    </div>
    <hr class="mt-4">
    <div class="mt-4 text-sm space-y-4">
        <div v-if="props.role === 1">
            <div>
                <InputLabel class="text-xs">Etapa de Funil</InputLabel>
                <select
                    v-model="form.funnelStep"
                    name="funnelStep"
                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"  
                    @change="onChange"                                                                           
                    >
                    <option 
                        v-for="funnelStep in funnelSteps"
                        :key="funnelStep.id" 
                        :value="`${funnelStep.id}`">
                        {{ funnelStep.name }}
                    </option>
                </select>
            </div>            
        </div>
        <div v-if="props.role === 2">
            <div 
                v-for="funnelStep in funnelSteps"
                :key="funnelStep.id" class=""
                > 
                <InputLabel v-if="funnelStep.id === lead.funnel_step_id" class="text-xs">Etapa de Funil</InputLabel>
                <div v-if="funnelStep.id === lead.funnel_step_id" :style="`background-color: ${funnelStep.color};`" :class="`my-2 px-4 py-2 border text-white font-light text-xs rounded-md`">
                    <div>{{funnelStep.name}}</div>
                </div>
            </div>
            <div>
                <div v-if="lead.status_id != 8 && lead.status_id != 9 && lead.funnel_step_id === 1">
                    <InputLabel class="text-xs">Ação</InputLabel>
                    <Button
                        variant="success"
                        class="flex w-full"           
                        style="background-color: #002060;"     
                        @click="setContactFunnel(2)"
                        title="Contato Feito">      
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                        </svg>      
                        <p class="font-light ml-1 text-xs"> Contato Feito</p>                                        
                    </Button>
                </div>
            </div>            
        </div>
        
        <div v-if="props.role === 1 || props.role === 4 || props.role === 3"  class="">
            <InputLabel class="text-xs">Responsável</InputLabel>
            <select
                v-model="form.user"
                name="user"
                class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"  
                @change="onChangeUser($event, lead)"                                                                           
                >
                <option 
                    v-for="item in users"
                    :key="item.id" 
                    :value="`${item.id}`">
                    {{ item.name }}
                </option>
            </select>
        </div>
        <div>
            <InputLabel class="text-xs">Classificação</InputLabel>
            <!-- 5 STARS -->
            <div class="flex space-x-1 mt-2 ">
                <Link
                    v-for="item in star"
                    :key="item"
                    href="#"
                    @click="setStars(lead, item)"
                    title="teste"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" :class="`${ item <= lead.stars ? 'text-amber-500' : 'text-slate-500'} w-6 h-6 hover:text-amber-500`">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </Link>                
            </div>
        </div>
        <div v-if="lead.form_name">
            <InputLabel class="text-xs">Formulário do Facebook</InputLabel>
            <div class="flex space-x-1 mt-2 bg-sky-700 text-white px-2 py-1 text-xxs rounded-sm">
                <span>{{ lead.form_name}}</span>
            </div>
        </div>
        <div class="flex mt-4 space-x-2">
            <Badge class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md sm:text-xxs md:text-xs font-light">{{ lead.channel }}</Badge>                               
            <Badge class="bg-slate-50 text-slate-600 px-1 py-1 rounded-md sm:text-xxs md:text-xs font-light">{{ lead.origem }}</Badge>                                    
        </div>
        <p class="mt-4 text-xs text-slate-400">Recebido: {{ moment(lead.created_at).format("DD/MM/YY HH:mm")}}</p>
        <span v-if="lead.interaction_date" class="text-xs text-slate-400">Última Interação: {{ moment(lead.interaction_date).format("DD/MM/YY HH:mm")}}</span>
    </div>
</template>

