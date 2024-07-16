<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { throttle, pickBy } from 'lodash';
import { Head, useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import Pagination from '@/Components/Pagination.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Alert from '@/Components/Alert.vue';
import Link from '@/Components/Link.vue';
import { ref, watch, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 1, name: 'Empresa', url : "/empresa"}});

const props = defineProps({
    companies: Object,
    filters: Object,
});

const filter = reactive({
    term: props.filters.term.term,
});

const form = useForm({
    id: null,
    status: null,
});

watch(filter,
    throttle( ()=>{
        let query = pickBy(filter);
        let queryRoute = route('company.index', Object.keys(query).length ? query : {
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

function destroy(id, status) {
    let msg = "Confirma a desativação desta empresa?";

    if(status === 1){
        msg = "Confirma a ativação desta empresa?";
    }else if(status === 3){
        msg = "Confirma a exclusão desta empresa?";
    }

    if (confirm(msg)) {
        form.status = status;
        form.delete(route('company.destroy', id));
    }
}

</script>

<template>
    <Head title="Empresas" />  

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pt-4 pb-6"/>
            
            <Alert v-if="$page.props.flash.message" type="info" @close="onClose()" class="mb-8">
                {{ $page.props.flash.message }}
            </Alert>

            <div class="bg-white">
                <div
                    class="relative overflow-x-auto shadow-md sm:rounded-lg"
                >   
                    <div class="grid grid-cols-7 gap-3 px-4 py-6 text-slate-600 ">
                        <div class="">
                            <ButtonLink 
                                variant="primary"
                                class="block"
                                value="/empresa/criar">NOVO
                            </ButtonLink>
                        </div>
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
                                <th scope="col" class="px-4 py-2.5 w-1">#</th>
                                <th scope="col" class="px-4 font-light">
                                    Nome
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Documento
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Responsável
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Campanha
                                </th>
                                <th scope="col" class="px-4 font-light">
                                    Status
                                </th>
                                <th scope="col" class="px-4 w-64">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="company in companies.data"
                                :key="company.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-slate-600"
                            >
                                <td
                                    scope="row"
                                    class="px-4 py-4"
                                >
                                    <input type="checkbox" :id="`chk_${company.id}`" v-model="checked" />
                                </td>
                                <td
                                    scope="row"
                                    class="px-4 w-full"
                                >
                                    {{ company.name }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4 whitespace-nowrap"
                                >
                                    {{ company.document }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4 whitespace-nowrap"
                                >
                                    {{ company.accountable }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4 whitespace-nowrap"
                                >
                                    {{ company.campaign_name }}
                                </td>
                                <td
                                    scope="row"
                                    class="px-4 whitespace-nowrap"
                                >
                                    <span class="font-bold" :class="company.status_id === 1 ? 'text-green-500':'text-slate-400'">
                                        {{ company.status_name }}
                                    </span>   
                                </td>
                                <td class="flex py-4 space-x-2 pr-4">
                                    <ButtonLink
                                        v-if="company.status_id === 1"
                                        variant="basic"
                                        :href="`/empresa/${company.id}/editar`"
                                        class="" 
                                        title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </ButtonLink>
                                    <Button
                                        v-if="company.status_id === 1"
                                        variant="basic"
                                        @click="destroy(company.id, 2)"
                                        class="" 
                                        title="Desativar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                        </svg>
                                    </Button>
                                    <Button
                                        v-if="company.status_id != 1"
                                        variant="basic"
                                        @click="destroy(company.id, 1)"
                                        class="" 
                                        title="Ativar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                        </svg>
                                    </Button>
                                    <Button
                                         variant="basic"
                                        @click="destroy(company.id, 3)"
                                        class="" 
                                        title="Ativar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="w-full bg-white justify-end py-5">
                        <Pagination :links="companies.links"></Pagination>
                    </div>
                </div>
            </div>
        </div><br><br>
    </AuthenticatedLayout>
</template>
