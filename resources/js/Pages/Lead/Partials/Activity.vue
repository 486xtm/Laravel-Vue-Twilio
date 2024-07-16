<script setup>
import { ref, watch,  computed, onMounted  } from 'vue';
import moment from "moment";

const props = defineProps({
    logs: Object,
});

</script>
<template>
    <div class="pb-6"> 
        <div>
            <ul class="space-y-2">
                <li 
                    v-for="activity in logs" 
                    :key="activity.id"
                    class="py-6 text-xs whitespace-break-spaces border-slate-100 border-b-2">
                    <div class="flex space-x-2 font-semibold pb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                        <span class="text-sky-600 sm:text-xxs md:text-xs">{{ activity.action }}</span><span class="font-normal sm:text-xxs md:text-xs text-slate-600">por {{ activity.user_name }} {{ moment(activity.created_at).format("DD/MM/YYYY HH:mm") }}</span>
                    </div>
                    <div class="flex">
                        <p class="px-6 pt-1 text-slate-600">{{ activity.description }}</p>
                    </div>
                </li>
            </ul>
            <div v-if="!logs.length" class="flex text-sm text-slate-600 pt-4">
                Nenhuma atividade registrada até o momento
            </div>
        </div>
    </div>

</template>

