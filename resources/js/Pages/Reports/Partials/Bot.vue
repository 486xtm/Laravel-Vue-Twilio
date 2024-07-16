<script setup>
import Button from '@/Components/Button.vue';

const props = defineProps({
    data: Object    
});
    
</script>
<template>
    <div class="space-y-6">
        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">NA FILA DO BOT</span>
                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0"> {{data.waiting}} </h1>
            </div>
            <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">TOTAL DE LEADS ATENDIDOS</span>
                <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0"> {{data.totals}} </h1>
            </div>                  
        </div>
        <div>
            <h1 class="mb-2 text-xs font-semibold text-slate-500">INTERAÇÃO</h1>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">NÃO INTERAGIRAM COM O BOT</span>
                    <div class="flex">
                        <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0">{{data.no_interact}}</h1>                                          
                    </div>
                </div>
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">RESPONDERAM O BOT</span>
                    <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0"> {{data.interact}}</h1>
                </div>
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">CONCLUIRAM O BOT</span>
                    <h1 class="text-sky-500 font-semibold sm:text-3xl md:text-5xl tracking-tighter sm:py-1 md:py-0">{{data.conclued > 0 ? data.conclued : 0}}</h1>            
                </div>                
            </div>    
        </div>        
    </div>
</template>