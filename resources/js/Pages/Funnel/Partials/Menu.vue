<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Button from '@/Components/Button.vue';
</script>
<template>
    <Dropdown>
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center border border-transparent text-xs leading-4 font-medium rounded-md text-slate-500 bg-white hover:text-slate-700 focus:outline-none transition ease-in-out duration-150"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                    </svg>
                </button>
            </span>
        </template>
        <template #content>
            <DropdownLink class="text-xs text-right" :href="route('cidade.index')"> Editar Lead </DropdownLink>
            <DropdownLink class="text-xs text-right" :href="route('cidade.index')"> Cadastrar Proposta </DropdownLink>
            <DropdownLink class="text-xs text-right" :href="route('cidade.index')"> Negócio Fechado </DropdownLink>
            <DropdownLink class="text-xs text-right" :href="route('cidade.index')"> Arquivar Lead </DropdownLink>
        </template>
    </Dropdown>
</template>