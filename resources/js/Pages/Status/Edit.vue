<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import { ref } from 'vue';

const breadcrumb = ref({ page:{id: 1, name: 'Configurações', url : "/configuracao"}, link:{id: 1, name: 'Status', url : "/status"}});
const props = defineProps({
    status: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
   name: props.status.name
});

function updateStatus(){
    form.put(route("status.update", props.status.id));
};

</script>

<template>
    <Head title="Produto" />
    <AuthenticatedLayout>            
        <div class="pt-4 max-w-7xl mx-auto sm:px-4 lg:px-10">
            <breadcrumb :value="breadcrumb" type="back" class="pb-5"/>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="updateStatus">
                        <div class="border-b border-slate-900/10 pb-12">
                            <h2 class="text-base font-semibold leading-7 text-sky-900">Informações do Status</h2>
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
                                    value="/status">VOLTAR
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
