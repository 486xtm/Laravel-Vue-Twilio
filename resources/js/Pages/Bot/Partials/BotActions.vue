<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import DropdownButton from '@/Components/DropdownButton.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';

const props = defineProps({
    bot: Object,
});

const form = useForm({
    id: null,
});

function turnOff(bot){
    if (confirm("Confirma a desativação do Bot?")) {
        form.id = bot.id;
        form.put(route("bot.status", bot.id));
    }
}

function turnOn(bot){
    if (confirm("Confirma a ativação do Bot?")) {
        form.id = bot.id;
        form.put(route("bot.status", bot.id));
    }
}


</script>
<template>
    <Dropdown>
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center px-0 py-0 border border-transparent text-xs leading-4 font-medium rounded-md text-slate-500 bg-white hover:text-slate-700 focus:outline-none transition ease-in-out duration-150"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                    </svg>
                </button>
            </span>
        </template>
        <template #content>
            <DropdownLink class="text-xs" :href="`/bot/${bot.id}/editar`"> Editar</DropdownLink>
            <DropdownButton v-if="bot.status_id === 1" class="text-xs" type="button" @click="turnOff(bot)"> Desativar </DropdownButton>
            <DropdownButton v-if="bot.status_id === 2" class="text-xs" type="button" @click="turnOn(bot)"> Ativar </DropdownButton>
        </template>
    </Dropdown>
</template>

