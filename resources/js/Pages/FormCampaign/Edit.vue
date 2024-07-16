<script setup>
import { ref  } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 2, name: 'Formulário', url : "/formulario"}});

const props = defineProps({
    form: {
        type: Object,
        default: () => ({}),
    },
    campaigns: {
        type: Object,
        default: () => ({}),
    },
    products: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
   name: props.form.name,
   description: props.form.description,
   facebook_form_id: props.form.facebook_form_id,
   campaign_id: props.form.campaign_id,
   product_id: props.form.product_id,
});

function update(){
    form.put(route("formcampaign.update", props.form.id));
};

</script>

<template>
    <Head title="Formulários" />
 
    <AuthenticatedLayout>
        <div class="pt-4 max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="update">
                        <div class="">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Informações do Formulário</h2>
                            <div class="mt-3 grid grid-cols-1">
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
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.name }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <Inputlabel
                                    for="Mensagem"
                                    class="block mb-2 text-sm font-mecold dium text-slate-700 dark:text-slate-300"
                                    >Descrição
                                </Inputlabel>
                                <textarea
                                    v-model="form.description"
                                    name="description"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"
                                    rows="3" 
                                    >
                                </textarea>
                                <div
                                    v-if="form.errors.description"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.description }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-3 space-x-2">
                            <div>
                                <Inputlabel
                                    for="User"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Campanha
                                </Inputlabel>
                                <select
                                    v-model="form.campaign_id"
                                    name="user"
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
                                    v-if="form.errors.campaign_id"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.campaign_id }}
                                </div>
                            </div>
                            <div>
                                <Inputlabel
                                    for="User"
                                    class="block mb-2 text-sm font-mecold dium text-slate-900 dark:text-slate-300"
                                    >Produto
                                </Inputlabel>
                                <select
                                    v-model="form.product_id"
                                    name="user"
                                    class="border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm text-sm w-full"                                        
                                    >
                                    <option :value="null">Selecione</option>
                                    <option 
                                        v-for="item in products"
                                        :key="item.id" 
                                        :value="`${item.id}`">
                                        {{ item.name }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.product_id"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.product_id }}
                                </div>
                            </div>
                            <div>
                                <TextInput
                                    textLabel="ID do Formulário no Facebook Leads"
                                    type="text"
                                    v-model="form.facebook_form_id"
                                    name="name"
                                    class="w-full"
                                    placeholder=""
                                />
                                <div
                                    v-if="form.errors.facebook_form_id"
                                    class="text-sm text-red-500 mt-2"
                                >
                                    {{ form.errors.facebook_form_id }}
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
                                    value="/formulario">VOLTAR
                                </ButtonLink>                                
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
