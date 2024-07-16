<script setup>
import { ref  } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 2, name: 'Cidade', url : "/cidade"}});

const form = useForm({
   nome: null,
   estado: null
});

const props = defineProps({
    estados: Object
});

function storeCidade(){
    form.post('/cidade');
}; 

</script>

<template>
    <Head title="Cidades" />

    <AuthenticatedLayout>
        <div class="pt-4 max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" type="back" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="storeCidade">
                        <div class="mb-6">
                            <Inputlabel
                                for="Nome"
                                class="block mb-2 text-sm font-mecold dium text-gray-900 dark:text-gray-300"
                                >Nome
                            </Inputlabel>
                            <TextInput
                                type="text"
                                v-model="form.nome"
                                name="nome"
                                class="w-full"
                                placeholder=""
                            />
                            <div
                                v-if="form.errors.nome"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.nome }}
                            </div>
                        </div>
                        <div class="mb-6">
                            <Inputlabel
                                for="Estado"
                                class="block mb-2 text-sm font-mecold dium text-gray-900 dark:text-gray-300"
                                >Estado
                            </Inputlabel>
                            <select
                                v-model="form.estado"
                                name="estado"
                                class="border-gray-300 focus:border-blue-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm w-full"
                                
                                >
                                <option :value="null">Selecione</option>
                                <option 
                                    v-for="estado in estados"
                                    :key="estado.id" 
                                    :value="`${estado.id}`">
                                    {{ estado.nome }}
                                </option>
                            </select>
                            <div
                                v-if="form.errors.estado"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.nome }}
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
                                    value="/cidade">VOLTAR
                                </ButtonLink>                                
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
