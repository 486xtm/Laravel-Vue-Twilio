<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import DropdownButton from '@/Components/DropdownButton.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';
import { replace } from 'lodash';
import moment from "moment";

const props = defineProps({
    lead: Object,
});

const favoriteForm = useForm({
    favorite: null,
});

const archiveForm = useForm({
    status: null,
});

const form = useForm({
    id: null,
});

const funnelForm = useForm({
    id: null,
});

function setArchive(lead){
    if (confirm("Confirma o arquivamento do lead?")) {
        archiveForm.status = 8;
        archiveForm.put(route("lead.archive", lead.id));
    }
}

function setContactFunnel(lead) {
    funnelForm.lead_id = lead.id;
    funnelForm.funnelStep = 2;
    funnelForm.put(route("lead.funnelstep", funnelForm));   
    emit('reloadLeads');     
}

function setFavorite(lead){
   favoriteForm.favorite = !lead.favorite;
   favoriteForm.put(route("lead.favorite", lead.id));
}

const emit = defineEmits(['reloadLeads']);

function destroyLead(lead) {
    if (confirm("Confirma a exclusão do usuário?")) {
        form.delete(route("lead.destroy", lead.id));
        emit('reloadLeads');    
    }
}

const whatsappLink = computed(() => {
    //console.log(props.lead);
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



</script>
<template>
    <Button
        v-if="(lead.status_id != 8 && lead.status_id != 9) && lead.funnel_step_id === 1"
        variant="basic"
        type="button"
        class="px-0.5"
        title="Contato Feito"        
        @click="setContactFunnel(lead)"
        >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
        </svg>
    </Button>
    <a
        :href="whatsappLink"
        class="text-slate-600 hover:text-sky-500 pt-2.5 pr-1"
        title="Whatsapp"
        target="_blank">    
        <span class="text-sm"><font-awesome-icon class="w-5 h-5" icon="fa-brands fa-whatsapp" /></span>
    </a>
    
    <Button
        v-if="lead.status_id != 8"
        variant="basic"
        type="button"
        :class="lead.favorite ? 'text-yellow-300':''"
        @click="setFavorite(lead)"
        >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
        </svg>
    </Button>    
    <Dropdown>
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center px-0 py-2 border border-transparent text-xs leading-4 font-medium rounded-md text-slate-500 bg-white hover:text-slate-700 focus:outline-none transition ease-in-out duration-150"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                    </svg>
                </button>
            </span>
        </template>
        <template #content>
            <DropdownLink v-if="lead.status_id != 8 && lead.status_id != 9" class="text-xs" href="" @click="setArchive(lead)"> Arquivar Lead </DropdownLink>
            <DropdownLink class="text-xs" :href="`/lead/${lead.id}/editar`"> Editar Lead </DropdownLink>
            <DropdownLink class="text-xs" :href="route('lead.view', lead.id)"> 
                Histórico </DropdownLink>
            <DropdownButton v-if="lead.status_id != 9" class="text-xs" type="button" @click="$emit('openProposal', lead)"> Cadastrar Proposta </DropdownButton>
            <DropdownLink v-if="$page.props.user.roles[0] === 'admin'" class="text-xs" href="" @click="destroyLead(lead)"> Excluir Lead </DropdownLink>
        </template>
    </Dropdown>
</template>

