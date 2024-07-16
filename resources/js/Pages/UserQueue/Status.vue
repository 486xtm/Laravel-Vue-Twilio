<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { throttle, pickBy } from 'lodash';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import Pagination from '@/Components/Pagination.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Link from '@/Components/Link.vue';
import { ref, watch, onMounted, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import moment from "moment";

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 1, name: 'Status na Fila', url : "/usuario/status"}});

const props = defineProps({
    users: Object,
    filters: Object,
});

const filter = reactive({
    term: props.filters.term.term,
});

const form = useForm({
    id: null,
    route: 'userqueue.status'
});

watch(filter,
    throttle( ()=>{
        let query = pickBy(filter);
        let queryRoute = route('userqueue.status', Object.keys(query).length ? query : {
            remember: 'forget',
        });
        
        router.get(
            queryRoute, 
            {},
            {
            preserveState:true,
            replace:true 
        });
    }, 500),
    {
        deep: true,
    }
); 

function pause(user) {
    if (confirm("Confirma a pausa deste usuário?")) {
        form.put(route('user.pause', user));
    }
}

function play(user) {
    if (confirm("Confirma ativação deste usuário?")) {
        form.put(route('user.play', user));    
    }
}

</script>

<template>
    <Head title="Usuários" />  

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            <div class="bg-white">
                <div
                    class="relative overflow-x-auto shadow-md sm:rounded-lg"
                >   
                    <div class="grid grid-cols-7 gap-3 px-4 py-6 text-slate-600 ">
                        <div class="col-span-3">
                            <TextInput
                                    type="text"
                                    name="search"
                                    v-model="filter.term"
                                    class="w-full"
                                    placeholder="Pesquisar"
                                />
                        </div>
                    </div>
                    <table
                        class="w-full text-sm text-left font-light text-gray-500 dark:text-gray-400"
                    >
                        <thead
                            class="text-slate-600 uppercase bg-slate-300 dark:bg-gray-700 dark:text-gray-400"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-2.5 w-2">#</th>
                                <th scope="col" class="px-6 font-light">
                                    Nome
                                </th>                                                           
                                <th scope="col" class="px-6 font-light">
                                    Perfil
                                </th>
                                <th scope="col" class="px-6 w-64 font-light">
                                    Ação
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                :class="user.status_id === 1 ? 'text-slate-600' : 'text-slate-300'"
                                class="bg-white border-b border-slate-100 even:bg-slate-50 odd:bg-white"
                            >
                                <td
                                    scope="row"
                                    class="px-6 py-4"
                                >
                                    {{ user.id }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-6 w-full"
                                >
                                    {{ user.name }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-6 whitespace-nowrap"
                                >
                                    {{ user.role }}
                                </td>
                                
                                <td class="flex justify-end px-6 py-4">
                                    <Button
                                        v-if="user.status_id === 1"    
                                        variant="basic"
                                        @click="pause(user)"
                                        class="" 
                                        title="Pausar Fila" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </Button>
                                    <Button
                                        v-if="user.status_id === 2"    
                                        variant="basic"
                                        @click="play(user)"
                                        class="text-slate-600" 
                                        title="Retomar Fila" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" />
                                        </svg>
                                    </Button>                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="w-full bg-white justify-end py-5">
                        <Pagination :links="users.links"></Pagination>
                    </div>
                </div>
            </div>
        </div><br><br>
    </AuthenticatedLayout>
</template>
