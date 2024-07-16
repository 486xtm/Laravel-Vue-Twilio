<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import moment from "moment";
import TextInput from '@/Components/TextInput.vue';
import Pagination from '@/Components/Pagination.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Link from '@/Components/Link.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Fila de Atendimento', url : "/fila"}});
let i = 1;

const props = defineProps({
    users: Object
});

</script>

<template>
    <Head title="Fila de Atendimento" />  

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            <div class="bg-white">
                <div
                    class="relative overflow-x-auto shadow-md p-2"
                >   
                    <div class="bg-sky-50 p-4 text-xs mb-2">
                        <p class="font-semibold">Regras:</p>
                        <ul class="py-2">
                            <li>1 - Ao receber lead de captação, não muda a posição na fila.</li>
                            <li>2 - Ao receber lead de cliente que já está em atendimento, não muda posição na fila.</li>
                            <li>3 - Se o usuário criar um lead para ele mesmo, não perde a vez na fila.</li>
                            <li>4 - Encaminhar lead não muda a posição na fila.</li>
                        </ul>
                        <p>* O próximo a receber pode variar devido a outras regras configuradas</p>
                    </div>
                
                    <table
                        class="w-full text-sm text-left font-light text-gray-500 dark:text-gray-400 whitespace-nowrap"
                    >
                        <thead
                            class="text-slate-600 uppercase bg-slate-300 dark:bg-gray-700 dark:text-gray-400"
                        >
                            <tr>
                                <th scope="col" class="px-6 font-light py-2.5">Posição</th>
                                <th scope="col" class="px-6 font-light">
                                    Nome
                                </th>
                                <th scope="col" class="px-6 font-light">
                                    Último Lead
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in users"
                                :key="user.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-slate-600"
                            >
                                <td
                                    scope="row"
                                    class="px-6 py-4"
                                >
                                    {{ i++ }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-6 w-full"
                                >
                                    {{ user.name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{moment(user.last_interaction).format("DD/MM/YYYY HH:mm:ss") }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="w-full bg-white justify-end py-5">
                        <!--<Pagination></Pagination>-->
                    </div>
                </div>
            </div>
        </div><br><br>
    </AuthenticatedLayout>
</template>
