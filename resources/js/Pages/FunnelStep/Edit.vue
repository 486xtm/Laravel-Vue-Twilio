<script setup>
import { ref  } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 1, name: 'Etapas de Funíl', url : "/etapa-de-funil"}});

const props = defineProps({
    step: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
   name: props.step.name,
   order: props.step.order,
   color: props.step.color,
});

function updateForm(){
    form.put(route("funnelstep.update", props.step.id));
};

</script>

<template>
    <Head title="Etapas de Funil" />
    <AuthenticatedLayout>            
        <div class="pt-4 max-w-7xl mx-auto sm:px-4 lg:px-6">
            <breadcrumb :value="breadcrumb" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="updateForm">
                        <div class="border-b border-slate-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Informações de Etapas de Funil</h2>
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
                            <div class="mt-3 grid grid-cols-6 space-x-2">
                                <div>
                                    <TextInput
                                        textLabel="Cor (#FFFFFF)"
                                        type="text"
                                        v-model="form.color"
                                        name="color"
                                        class="w-full"
                                        placeholder="#FFFFFF"
                                    />
                                    <div
                                        v-if="form.errors.color"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ form.errors.color }}
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-mecold dium text-slate-600 dark:text-slate-300"
                                        >Ordenação
                                    </label>
                                    <input
                                        type="number"
                                        v-model="form.order"
                                        name="order"
                                        class="border border-slate-300 px-1.5 py-1.5 rounded-md focus:outline-none focus:border-sky-500 focus:border-2 focus:ring-sky-500 w-full text-sm text-slate-700 placeholder:text-slate-400"
                                        placeholder=""
                                    />
                                    <div
                                        v-if="form.errors.order"
                                        class="text-sm text-red-500 mt-2"
                                    >
                                        {{ form.errors.order }}
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
                                    value="/etapa-de-funil">VOLTAR
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
