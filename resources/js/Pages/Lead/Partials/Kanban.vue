<script setup>
import { ref, watch, reactive, computed } from 'vue';
import {useForm } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import { router } from '@inertiajs/vue3';
import Link from '@/Components/Link.vue';

let term = ref('');
const name = ref("two-lists");
const display = ref("Two Lists");
const order = ref(1);
const id = ref(0);

const cardlist = ref(props.lists); 

const props = defineProps({
    lists:Object,
})

const form = useForm({
   lead_id: null,
   funnel_step_id: null   
});


const onMove = function(ev) {
    console.log(ev.relatedContext.list);
}

const onChange = function(e){
    console.log(props.lists);

    let item = e.added || e.moved;

    if(!item) return;
    id.value = item.element.id;
    console.log(item);
   
    //form.put(route("funnel.move", props.lists.id));    
}
</script>
<template>
    <div class="flex space-x-4 mt-5">
        <!-- Kanban columns -->
        <div class="flex-1" 
            v-for="list in cardlist"
            :key="list.id"    
            >
            <h2 :class="`bg-${list.color}-400 text-white rounded text-md font-semibold p-3 mb-2`">{{ list.name}}</h2>
            <div class="bg-gray-100 rounded py-4 cursor-move">
                <draggable
                    class="space-y-2"
                    tag="ul"
                    v-model="list.lead"
                    group="leads"
                    :move="onMove"
                    @change="onChange"
                    item-key="id"
                >
                    <template #item="{ element }">
                        <li class="p-4 border-l-2 border-red-400 bg-white shadow">
                            <div class="flex justify-between">
                                <Link :href="`/lead/${element.id}/`" class="font-bold text-sky-900">{{ element.name }}</Link>
                                <p class="text-xs text-gray-500">{{ element.product }}</p>
                            </div>
                            <p class="pb-5 text-xs text-sky-900">{{ element.product }}</p>
                            
                            <div class="flex justify-between text-slate-400 align-baseline">
                                <div class="flex space-x-2">
                                    <Badge class="bg-slate-100 text-indigo-900 px-1 py-1 rounded-none text-xs font-light">Rede Social</Badge>
                                    <Badge class="bg-slate-100 text-indigo-900 px-1 py-1 rounded-none text-xs font-light">Instagram Leads</Badge>
                                </div>
                                <div class="flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    <p class="text-xs pl-1">{{ element.usuario }}</p>
                                </div>
                            </div>
                        </li>
                    </template>
                </draggable>
            </div>
        </div>
    </div>
</template>