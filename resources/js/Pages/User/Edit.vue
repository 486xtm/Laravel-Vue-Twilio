<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref,reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 1, name: 'Usuários', url : "/usuario"}});

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
    companies: {
        type: Object,
        default: () => ({}),
    },
    roles: {
        type: Object,
        default: () => ({}),
    },
    managers: {
        type: Object,
        default: () => ({}),
    }
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    role: props.user.role_id,
    manager: props.user.manager_id,
    company: props.user.company_id,
});

function updateUser(){
    form.put(route("user.update", props.user.id));
};

</script>

<template>
    <Head title="Usuários" />

    <AuthenticatedLayout>
        <div class="pt-5 max-w-7xl mx-auto sm:px-4 lg:px-6">
            <breadcrumb :value="breadcrumb" type="back" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="updateUser">
                            <div class="border-b border-slate-900/10 pb-12">
                                <h2 class="text-base font-semibold leading-7 text-sky-900">Informações do Usuário</h2>
                                <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="sm:col-span-4">
                                        <TextInput
                                            textLabel="Nome"
                                            type="text"
                                            v-model="form.name"
                                            name="name"
                                            class="w-full"
                                            placeholder=""
                                            disabled="true"
                                        />
                                        <div
                                            v-if="form.errors.name"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.name }}
                                        </div>
                                    </div>
                                    <div class="sm:col-span-4">
                                    <TextInput
                                            textLabel="Email"
                                            type="text"
                                            v-model="form.email"
                                            name="email"
                                            class="w-full"
                                            disabled="true"
                                        />
                                        <div
                                            v-if="form.errors.email"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.email }}
                                        </div>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <TextInput
                                            textLabel="Celular"
                                            type="text"
                                            v-model="form.phone"
                                            name="phone"
                                            class="w-full"
                                            mask="(##)#####-####"
                                        />
                                        <div
                                            v-if="form.errors.phone"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.phone }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-b border-slate-900/10 pb-12 mt-5">
                                <h2 class="text-base font-semibold leading-7 text-sky-900">Regras de Acesso</h2>
                                
                                <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <Inputlabel
                                            for="role"
                                            class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                            >Permissão
                                        </Inputlabel>
                                        <select
                                            v-model="form.role"
                                            name="role"
                                            class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                            >
                                            <option :value="null">Selecione</option>
                                            <option 
                                                v-for="role in roles"
                                                :key="role.id" 
                                                :value="`${role.id}`">
                                                {{ role.name }}
                                            </option>
                                        </select>
                                        <div
                                            v-if="form.errors.role"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.role }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-b border-slate-900/10 pb-12 mt-5" v-if="parseInt(form.role) === 2">
                                <h2 class="text-base font-semibold leading-7 text-sky-900">Equipe de Trabalho</h2>
                                
                                <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <Inputlabel
                                            for="role"
                                            class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                            >Gerente
                                        </Inputlabel>
                                        <select
                                            v-model="form.manager"
                                            name="role"
                                            class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                            >
                                            <option :value="null">Não possui gerente associado</option>
                                            <option 
                                                v-for="item in managers"
                                                :key="item.id" 
                                                :value="`${item.id}`">
                                                {{ item.name }}
                                            </option>
                                        </select>
                                        <div
                                            v-if="form.errors.manager"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.manager }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-b border-slate-900/10 pb-12 mt-5">
                                <h2 class="text-base font-semibold leading-7 text-sky-900">Empresa Associada</h2>
                                
                                <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <Inputlabel
                                            for="company"
                                            class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                            >Empresa
                                        </Inputlabel>
                                        <select
                                            v-model="form.company"
                                            name="company"
                                            class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                            >
                                            <option :value="null">Selecione</option>
                                            <option 
                                                v-for="company in companies"
                                                :key="company.id" 
                                                :value="`${company.id}`">
                                                {{ company.name }}
                                            </option>
                                        </select>
                                        <div
                                            v-if="form.errors.company"
                                            class="text-sm text-red-600"
                                        >
                                            {{ form.errors.company }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-4 space-x-2">
                                    <Button
                                        variant="primary"
                                        type="submit"
                                        class=""
                                        :disabled="form.processing"
                                        :class="{ 'opacity-25': form.processing }"
                                    >
                                        SALVAR
                                    </Button>
                                    <ButtonLink 
                                        variant="default"
                                        class=""
                                        value="/usuario">VOLTAR
                                    </ButtonLink>                                   
                                </div>
                            </div>                        
                        </form>
                    </div>                
                </div>               
            </div>
        </div>
    </AuthenticatedLayout>
</template>