<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Leads', url : "/lead"}});

const props = defineProps({
    lead: {
        type: Object,
        default: () => ({}),
    },    
    users: {
        type: Object,
        default: () => ({}),
    },
    canais: {
        type: Object,
        default: () => ({}),
    },
    origens: {
        type: Object,
        default: () => ({}),
    },
    products: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
   name: props.lead.name,
   email: props.lead.email,
   telefone: props.lead.telefone,
   celular: props.lead.celular,
   origem: props.lead.origem_id,
   canal: props.lead.canal_id,
   user: props.lead.user_id,
   product: props.lead.product_id,
   mensagem: props.lead.mensagem,
});

function updateLead(){
    form.put(route("lead.update", props.lead.id));    
};

</script>

<template>
    <Head title="Leads" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" type="back" class="pt-4 pb-6"/>

            <div class="overflow-hidden bg-white shadow-md sm:rounded-lg mb-10">
                <div class="p-6 bg-white border-b border-slate-200">
                    <form @submit.prevent="updateLead">
                        <div class="border-b border-slate-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Informações do lead</h2>
                         
                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="md:col-span-4 sm:col-span-6">
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
                                <div class="md:col-span-4 sm:col-span-6">
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
                                <div class="md:col-span-1 sm:col-span-3">
                                    <TextInput
                                        textLabel="Telefone"
                                        type="text"
                                        v-model="form.telefone"
                                        mask="(##)####-####"
                                        name="telefone"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.telefone"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.telefone }}
                                    </div>
                                </div>
                                <div class="md:col-span-1 sm:col-span-3">
                                    <TextInput
                                        textLabel="Celular"
                                        type="text"
                                        v-model="form.celular"
                                        name="celular"
                                        mask="(##)#####-####"
                                        class="w-full"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.celular"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.celular }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-slate-900/10 pb-12 mt-5">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Produto</h2>
                            
                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="md:col-span-4 sm:col-span-6">
                                    <Inputlabel
                                        for="product"
                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                        >Consórcio
                                    </Inputlabel>
                                    <select
                                        v-model="form.product"
                                        name="product"
                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                        >
                                        <option :value="null">Selecione</option>
                                        <option 
                                            v-for="product in products"
                                            :key="product.id" 
                                            :value="`${product.id}`">
                                            {{ product.name }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.product"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.product }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-slate-900/10 pb-12 mt-5">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Forma de Entrada</h2>

                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="md:col-span-2 sm:col-span-3">
                                    <Inputlabel
                                        for="Canal"
                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                        >Canal
                                    </Inputlabel>
                                    <select
                                        v-model="form.canal"
                                        name="canal"
                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                        >
                                        <option :value="null">Selecione</option>
                                        <option 
                                            v-for="canal in canais"
                                            :key="canal.id" 
                                            :value="`${canal.id}`">
                                            {{ canal.name }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.canal"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.canal }}
                                    </div>
                                </div>
                                <div class="md:col-span-2 sm:col-span-3">
                                    <Inputlabel
                                        for="Origem"
                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                        >Origem
                                    </Inputlabel>
                                    <select
                                        v-model="form.origem"
                                        name="origem"
                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                        >
                                        <option :value="null">Selecione</option>
                                        <option 
                                            v-for="origem in origens"
                                            :key="origem.id" 
                                            :value="`${origem.id}`">
                                            {{ origem.name }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.origem"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.origem }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-slate-900/10 pb-12 mt-5">
                            <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="md:col-span-4 sm:col-span-6" v-if="$page.props.user.roles[0] === 'admin'">
                                    <h2 class="text-base font-semibold mb-5 leading-7 text-sky-900">Encaminhar</h2>
                                    <Inputlabel
                                        for="User"
                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                        >Usuário
                                    </Inputlabel>
                                    <select
                                        v-model="form.user"
                                        name="user"
                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"                                        
                                        >
                                        <option :value="null">Selecione</option>
                                        <option 
                                            v-for="user in users"
                                            :key="user.id"                                             
                                            :value="`${user.id}`">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.user"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.user }}
                                    </div>
                                </div>
                                <div class="sm:col-span-6">
                                    <Inputlabel
                                        for="Mensagem"
                                        class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                        >Observação
                                    </Inputlabel>
                                    <textarea
                                        v-model="form.mensagem"
                                        name="mensagem"
                                        class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                        rows="3" 
                                        >
                                    </textarea>
                                    <div
                                        v-if="form.errors.mensagem"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.mensagem }}
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
                                    value="/lead">VOLTAR
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
