<script setup>
import { ref, watch, reactive, computed } from 'vue';
import {useForm } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import { router } from '@inertiajs/vue3';
import Link from '@/Components/Link.vue';
import Actions from './LeadActions.vue';
import moment from "moment";

const cardlist = ref(props.lists); 
const props = defineProps({
    lists:Object,
})
</script>
<template v-for="lists in cardlist" :key="list.id">
    <div class="flex space-x-4 mt-5 sm:overflow-x-auto sm:max-w-screen-sm md:max-w-screen-2xl">
        <!-- Kanban columns -->
        <div class="flex-1" 
            v-for="list in lists"
            :key="list.id"    
            >
            <div :class="`bg-${list.color}-400 text-white rounded text-md font-semibold p-3 mb-2 justify-between flex`"><span>{{ list.name}}</span><span>{{ list.count }}</span></div>
            <div class="bg-gray-100 rounded py-4 sm:w-72">
                <ul
                    v-for="element in list.leads"
                    :key="element.id"
                    class="mt-3"
                >
                    <li :class="`border-l-2 border-${list.color}-400 bg-white shadow`">
                        <div class="flex py-2 justify-between">
                            <div class="mb-2 pl-4">
                                <Link :href="`/lead/${element.id}/`" class="font-bold text-sm text-sky-900">{{ element.name }}</Link>
                                <p class="text-xs text-gray-500 w-full">{{ element.product }}</p>
                            </div>
                            <div class="flex space-x-1 h-10 pr-1">
                                <Actions :lead="element"  @openProposal="$emit('openProposal', element)"></Actions>
                            </div>
                        </div>
                        <div class="px-4 py-2 flex justify-between text-slate-400 align-baseline">
                            <div class="flex space-x-2">
                                <span class="bg-slate-50 text-slate-600 py-1 rounded-md text-xs font-light px-2">{{ element.origem }}</span>
                            </div>
                            <div class="flex pt-2">
                                <p v-if="$page.props.user.roles[0] === 'admin'" class="text-xs pl-1">{{ element.usuario }}</p>
                            </div>
                        </div>
                        <div class="mt-1 px-4 py-2 text-xs text-slate-500 border-t border-slate-100">
                            <span v-if="list.id === 2 && element.schedule_date != null ">Retornar em {{ moment(element.schedule_date).format("DD/MM/YYYY HH:mm") }}</span>
                            <span v-else>Recebido em {{ moment(element.created_at).format("DD/MM/YYYY HH:mm") }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template> 