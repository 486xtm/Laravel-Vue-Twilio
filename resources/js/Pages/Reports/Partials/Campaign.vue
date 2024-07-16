<script setup>
import Button from '@/Components/Button.vue';

const props = defineProps({
    data: Object    
});
    
</script>
<template>
    <div class="space-y-6">
         <div>            
            <div class="grid sm:grid-cols-1 md:grid-cols-1 gap-4">
                <div class="bg-white shadow-sm border border-slate-200 px-6 py-3">
                    <span class="text-slate-500 text-xxs font-light leading-6 border-b-2 border-sky-800 pb-1">AGUARDANDO PROCESSAMENTO</span>
                    <div class="mt-6 lg:h-72 overflow-x-auto">
                        <table class="w-full text-sm text-left  text-slate-50 whitespace-nowrap">
                            <thead class="font-bold text-xs">
                                <tr class="bg-sky-900 shadow-sm">
                                    <th scope="col" class="px-2 py-2.5 w-2">
                                        Formulário
                                    </th>
                                    <th scope="col" class="px-2 py-2.5 w-2 text-right">
                                        Total
                                    </th>                                        
                                </tr>
                            </thead>                            
                            <tbody class="font-normal text-xs text-slate-500">
                                <tr
                                    v-for="form in data"
                                    :key="form.id"
                                    class="even:bg-slate-50 odd:bg-white"
                                >
                                    <td class="px-2 py-2.5">{{ form.name }}</td>
                                    <td class="px-2 py-2.5 text-right w-1/6">{{ form.total }}</td>                                   
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</template>