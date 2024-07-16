<script setup>
import { ref  } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 2, name: 'Empresa', url : "/empresa"}});

const props = defineProps({
    citys: Object,
    states: Object,
    campaigns: Object,
});

const form = useForm({
   name: null,
   document: null,
   address: null,
   number: null,
   complement: null,
   region: null,
   zip: null,
   city: null,
   state: null,
   accountable: null,
   email: null,
   phone: null,
   campaign: null,
});

function storeCompany(){
    form.post('/empresa');
};

</script>

<template>
    <Head title="Empresas" />

    <AuthenticatedLayout>
        <div class="pt-4 max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" type="back" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-10">
                <div class="p-6 bg-white border-b border-slate-200">
                    <form @submit.prevent="storeCompany">
                        <div class="border-b border-slate-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Informações da Empresa</h2>
                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-4">
                                    <TextInput
                                        textLabel="Nome"
                                        type="text"
                                        v-model="form.name"
                                        name="name"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.name"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.name }}
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                   <TextInput
                                        textLabel="CNPJ"
                                        type="text"
                                        v-model="form.document"
                                        name="document"
                                        class="w-full"
                                        placeholder=""
                                        mask="##.###.###/####-##"
                                    />
                                    <div
                                        v-if="form.errors.document"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.document }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-slate-900/10 pb-12 mt-5">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Endereço</h2>

                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-1">
                                   <TextInput
                                        textLabel="CEP"
                                        type="text"
                                        v-model="form.zip"
                                        name="zip"
                                        class="w-full"
                                        placeholder=""
                                        mask="##.###-###"
                                    />
                                    <div
                                        v-if="form.errors.zip"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.zip }}
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <TextInput
                                        textLabel="Endereço"
                                        type="text"
                                        v-model="form.address"
                                        name="address"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.address"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.address }}
                                    </div>
                                </div>
                                <div class="sm:col-span-1">
                                   <TextInput
                                        textLabel="Número"
                                        type="text"
                                        v-model="form.number"
                                        name="number"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.number"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.number }}
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                   <TextInput
                                        textLabel="Complemento"
                                        type="text"
                                        v-model="form.complement"
                                        name="complement"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.complement"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.complement }}
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                   <TextInput
                                        textLabel="Bairro"
                                        type="text"
                                        v-model="form.region"
                                        name="region"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.region"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.region }}
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <Inputlabel
                                        for="Cidade"
                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                        >Cidade
                                    </Inputlabel>
                                    <select
                                        v-model="form.city"
                                        name="city"
                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                        >
                                        <option :value="null">Selecione</option>
                                        <option 
                                            v-for="city in citys"
                                            :key="city.id" 
                                            :value="`${city.id}`">
                                            {{ city.nome }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.city"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.city }}
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <Inputlabel
                                        for="Estado"
                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                        >Estado
                                    </Inputlabel>
                                    <select
                                        v-model="form.state"
                                        name="state"
                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                        >
                                        <option :value="null">Selecione</option>
                                        <option 
                                            v-for="state in states"
                                            :key="state.id" 
                                            :value="`${state.id}`">
                                            {{ state.nome }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.state"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.state }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-slate-900/10 pb-12 mt-5">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Responsável</h2>

                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-6">
                                    <TextInput
                                        textLabel="Nome"
                                        type="text"
                                        v-model="form.accountable"
                                        name="accountable"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.accountable"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.accountable }}
                                    </div>
                                </div>
                                <div class="sm:col-span-3">
                                    <TextInput
                                        textLabel="Email"
                                        type="text"
                                        v-model="form.email"
                                        name="email"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.email"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <TextInput
                                        textLabel="Telefone"
                                        type="text"
                                        v-model="form.phone"
                                        name="phone"
                                        class="w-full"
                                        mask="(##)#####-####"
                                        placeholder=""
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
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Campanhas de Marketing</h2>

                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-6">
                                    <Inputlabel
                                        for="Cidade"
                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                        >Campanhas
                                    </Inputlabel>
                                    <select
                                        v-model="form.campaign"
                                        name="campaign"
                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                        >
                                        <option :value="null">Selecione</option>
                                        <option 
                                            v-for="item in campaigns"
                                            :key="item.id" 
                                            :value="`${item.id}`">
                                            {{ item.name }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.campaign"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.campaign }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-4 space-x-2">
                                <Button
                                    variant="primary"
                                    type="submit"
                                    :disabled="form.processing"
                                    :class="{ 'opacity-25': form.processing }"
                                >
                                    SALVAR
                                </Button>
                                <ButtonLink 
                                    variant="default"
                                    class=""
                                    value="/empresa">VOLTAR
                                </ButtonLink>                                
                            </div>
                        </div>  
                    </form>
                </div>                
            </div>
            <div class="flex justify-end mt-2">
            </div>
        </div>
    </AuthenticatedLayout>
</template>
