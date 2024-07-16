<script setup>
import { ref  } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 2, name: 'Campanha', url : "/campanha"}});

const props = defineProps({
    campaign: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
   name: props.campaign.name,
   description: props.campaign.description,
});


function update(){
    form.put(route("campaign.update", props.campaign.id));
};

</script>

<template>
    <Head title="Campanha" />
 
    <AuthenticatedLayout>
        <div class="pt-4 max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" type="back" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="update">
                        <div class="">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Informações da Campanha</h2>
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
                                    value="/campanha">VOLTAR
                                </ButtonLink>                                
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
